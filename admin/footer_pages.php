<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/footer_pages.php';

if (!ensureFooterPagesSchema($pdo)) {
    http_response_code(503);
    exit('Footer page setup is not available. Check database permissions and reload this page.');
}

require_once __DIR__ . '/includes/auth-check.php';
checkAdminPermission('manage_footer_pages');

$_SESSION['footer_pages_csrf'] = $_SESSION['footer_pages_csrf'] ?? bin2hex(random_bytes(32));
$csrf = $_SESSION['footer_pages_csrf'];
$error = '';
$success = $_SESSION['footer_pages_flash'] ?? '';
unset($_SESSION['footer_pages_flash']);

function footer_pages_h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function footer_pages_form(array $page = []): array
{
    $styles = footerPageStyleSettings($page);
    return [
        'id' => (int)($page['id'] ?? 0),
        'title' => (string)($page['title'] ?? ''),
        'slug' => (string)($page['slug'] ?? ''),
        'content' => (string)($page['content'] ?? ''),
        'sort_order' => (int)($page['sort_order'] ?? 0),
        'is_active' => !array_key_exists('is_active', $page) || (int)$page['is_active'] === 1,
        'legacy_path' => (string)($page['legacy_path'] ?? ''),
        'styles' => $styles,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $redirectUrl = 'footer_pages.php';
        if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
            throw new RuntimeException('Your session expired. Refresh the page and try again.');
        }

        $action = (string)($_POST['action'] ?? '');
        if ($action === 'delete') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!$id) {
                throw new RuntimeException('Choose a valid page.');
            }
            $stmt = $pdo->prepare('DELETE FROM footer_pages WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->rowCount()) {
                throw new RuntimeException('Footer page not found.');
            }
            $_SESSION['footer_pages_flash'] = 'Footer page deleted. Its link has been removed from the footer.';
        } elseif ($action === 'save') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
            $title = trim((string)($_POST['title'] ?? ''));
            $slug = footerPageSlug((string)($_POST['slug'] ?? ''));
            $content = sanitizeFooterPageContent((string)($_POST['content'] ?? ''));
            $sortOrder = max(0, (int)($_POST['sort_order'] ?? 0));
            $isActive = !empty($_POST['is_active']) ? 1 : 0;
            $styleDefaults = footerPageStyleSettings([]);
            $styles = [];
            foreach (['title_color', 'subtitle_color', 'description_color', 'bullet_color'] as $key) {
                $value = (string)($_POST[$key] ?? $styleDefaults[$key]);
                if (!preg_match('/^#[0-9a-f]{6}$/i', $value)) {
                    throw new RuntimeException('Choose a valid color for page appearance.');
                }
                $styles[$key] = $value;
            }
            foreach (['title_size' => [18, 48], 'subtitle_size' => [14, 36], 'description_size' => [12, 24]] as $key => $range) {
                $styles[$key] = max($range[0], min($range[1], (int)($_POST[$key] ?? $styleDefaults[$key])));
            }
            $styleSettings = json_encode($styles, JSON_UNESCAPED_SLASHES);

            if ($title === '' || mb_strlen($title) > 150) {
                throw new RuntimeException('Enter a page title of 1 to 150 characters.');
            }
            if ($slug === '' || strlen($slug) > 160) {
                throw new RuntimeException('Enter a valid URL slug using letters, numbers, or hyphens.');
            }
            if (mb_strlen($content) > 50000) {
                throw new RuntimeException('Page content must be 50,000 characters or less.');
            }

            if ($id) {
                $exists = $pdo->prepare('SELECT id FROM footer_pages WHERE id = ?');
                $exists->execute([$id]);
                if (!$exists->fetchColumn()) {
                    throw new RuntimeException('Footer page not found.');
                }
                $stmt = $pdo->prepare('UPDATE footer_pages
                    SET title = ?, slug = ?, content = ?, style_settings = ?, sort_order = ?, is_active = ?
                    WHERE id = ?');
                $stmt->execute([$title, $slug, $content === '' ? null : $content, $styleSettings, $sortOrder, $isActive, $id]);
                $_SESSION['footer_pages_flash'] = 'Footer page updated successfully.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO footer_pages (title, slug, content, style_settings, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$title, $slug, $content === '' ? null : $content, $styleSettings, $sortOrder, $isActive]);
                $id = (int)$pdo->lastInsertId();
                $_SESSION['footer_pages_flash'] = 'Footer page created successfully.';
            }
            $redirectUrl = 'footer_pages.php?edit=' . (int)$id;
        } else {
            throw new RuntimeException('Invalid action.');
        }

        // Stay on the page that was just saved, so the administrator can continue editing.
        header('Location: ' . $redirectUrl);
        exit;
    } catch (PDOException $e) {
        $error = $e->getCode() === '23000'
            ? 'That URL slug is already in use. Choose a different slug.'
            : 'Unable to save the footer page. Please try again.';
        error_log('Footer page admin action failed: ' . $e->getMessage());
    } catch (Throwable $e) {
        $error = $e instanceof RuntimeException ? $e->getMessage() : 'Unable to save the footer page. Please try again.';
        if (!($e instanceof RuntimeException)) {
            error_log('Footer page admin action failed: ' . $e->getMessage());
        }
    }
}

$editing = null;
$editId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM footer_pages WHERE id = ?');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    if (!$editing && !$error) {
        $error = 'Footer page not found.';
    }
}
$form = footer_pages_form($editing ?: []);
$pages = $pdo->query('SELECT * FROM footer_pages ORDER BY sort_order, id')->fetchAll(PDO::FETCH_ASSOC);
$previewUrl = '';
if ($editing) {
    $previewUrl = '../' . footerPageUrl($editing);
}
$previewSelector = $editing ? '.' . footerPageContentClass($editing) : '';
$sectionClassMap = [
    'about.php' => 'about-section',
    'returns.php' => 'policy-section',
    'privacy.php' => 'privacy-section',
    'faq.php' => 'faq-section',
];
$sectionClass = $editing ? ($sectionClassMap[$editing['legacy_path'] ?? ''] ?? 'footer-editor-section') : 'footer-editor-section';
$isFaqEditor = $editing && (($editing['legacy_path'] ?? '') === 'faq.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Pages - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .footer-page-content { display:none; }
        .footer-page-status { font-size: .78rem; }
        .footer-page-title { max-width: 260px; }
        .footer-page-url { overflow-wrap: anywhere; }
        .footer-page-visual-editor { background:#fff; border:1px solid #bdc9d6; border-radius:0 0 6px 6px; min-height:360px; outline:0; padding:28px; }
        .footer-page-visual-editor:focus { border-color:#0d6efd; box-shadow:0 0 0 .2rem rgba(13,110,253,.16); }
        .footer-page-editor-toolbar { background:#f8fafc; border:1px solid #bdc9d6; border-bottom:0; border-radius:6px 6px 0 0; display:flex; flex-wrap:wrap; gap:6px; padding:8px; }
        .footer-page-visual-editor h2 { border-left:4px solid #1683e8; color:#2c539f; font-size:1.45rem; padding-left:12px; }
        .footer-page-visual-editor p, .footer-page-visual-editor li { line-height:1.65; }
        .footer-page-visual-editor a { color:#0d6efd; text-decoration:underline; }
        .footer-page-visual-editor li::marker { color:#1683e8; }
        .footer-page-visual-editor .about-section,
        .footer-page-visual-editor .policy-section,
        .footer-page-visual-editor .privacy-section,
        .footer-page-visual-editor .faq-section,
        .footer-page-visual-editor .footer-editor-section { border:1px dashed #9db6ce; border-radius:6px; margin:24px 0; padding:28px 14px 12px; position:relative; }
        .footer-page-visual-editor .about-section::before,
        .footer-page-visual-editor .policy-section::before,
        .footer-page-visual-editor .privacy-section::before,
        .footer-page-visual-editor .faq-section::before,
        .footer-page-visual-editor .footer-editor-section::before { background:#e8f2ff; border:1px solid #9db6ce; border-radius:12px; color:#285a8a; content:'Editable section'; font-size:.72rem; font-weight:700; left:10px; padding:2px 8px; position:absolute; top:-11px; }
        .faq-builder-section { border:1px solid #cfd8e3; border-radius:8px; margin-bottom:18px; padding:16px; }
        .faq-builder-question { background:#f8fafc; border:1px solid #dbe4ee; border-radius:6px; margin-top:12px; padding:12px; }
        .faq-builder-answer { background:#fff; border:1px solid #bdc9d6; border-radius:5px; min-height:90px; padding:10px; }
        .footer-sections-swal { border-radius:10px !important; padding:18px !important; width:360px !important; }
        .footer-sections-swal .swal2-title { font-size:1.2rem !important; margin:4px 0 8px !important; }
        .footer-sections-swal .swal2-html-container { font-size:.86rem !important; line-height:1.4 !important; margin:0 0 12px !important; }
        .footer-sections-swal .swal2-icon { box-sizing:border-box !important; height:46px !important; margin:2px auto 8px !important; transform:none !important; width:46px !important; }
        .footer-sections-swal .swal2-actions { gap:7px !important; margin:8px 0 0 !important; }
        .footer-sections-swal .swal2-styled { font-size:.82rem !important; padding:8px 12px !important; }
        .footer-link-swal { border-radius:12px !important; padding:20px 22px !important; width:420px !important; }
        .footer-link-swal .swal2-title { color:#25324a; font-size:1.25rem !important; line-height:1.2; margin:0 0 14px !important; }
        .footer-link-swal .swal2-html-container { margin:0 !important; overflow:visible !important; }
        .footer-link-form { display:grid; gap:11px; text-align:left; }
        .footer-link-form label { color:#4b5563; display:block; font-size:.78rem; font-weight:600; margin:0 0 4px; }
        .footer-link-form .footer-link-field { border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box; color:#1f2937; font-size:.9rem; height:38px; margin:0; padding:7px 10px; width:100%; }
        .footer-link-form .footer-link-field:focus { border-color:#0d6efd; box-shadow:0 0 0 .16rem rgba(13,110,253,.14); outline:0; }
        .footer-link-swal .swal2-actions { gap:8px; margin:16px 0 0 !important; }
        .footer-link-swal .swal2-styled { font-size:.84rem !important; margin:0 !important; padding:8px 14px !important; }
    </style>
</head>
<body>
<div class="everythingb2c-admin-container">
    <?php include 'includes/sidebar.php'; ?>
    <div class="everythingb2c-main-content">
        <?php include 'includes/header.php'; ?>
        <main class="container-fluid p-3 p-lg-4">
            <?php if ($error): ?><div class="alert alert-danger"><?php echo footer_pages_h($error); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo footer_pages_h($success); ?></div><?php endif; ?>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h4 class="mb-1">Footer Pages</h4>
                    <p class="text-muted mb-0">Create, edit, publish, reorder, or remove only the links shown in the website footer.</p>
                </div>
                <?php if (!$editing): ?><a href="footer_pages.php?edit=new" class="btn btn-primary"><i class="fas fa-plus"></i> Add Footer Page</a><?php endif; ?>
            </div>

            <?php if (isset($_GET['edit'])): ?>
            <section class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center gap-2">
                    <strong><?php echo $editing ? 'Edit Footer Page' : 'Add Footer Page'; ?></strong>
                    <a class="btn btn-sm btn-outline-secondary" href="footer_pages.php"><i class="fas fa-arrow-left"></i> Back to Footer Pages</a>
                </div>
                <div class="card-body">
                    <?php if ($editing && $form['legacy_path'] !== '' && $form['content'] === ''): ?>
                        <div class="alert alert-info"><?php echo $isFaqEditor ? 'The existing FAQ sections load automatically below. Edit each question and answer in its own box.' : 'Click <strong>Load Current Sections</strong>. The existing page sections will become editable here. After saving once, future edits are made directly in this visual editor.'; ?></div>
                    <?php endif; ?>
                    <form method="post" id="footerPageForm">
                        <input type="hidden" name="csrf" value="<?php echo footer_pages_h($csrf); ?>">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?php echo (int)$form['id']; ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="footerPageTitle">Link title *</label>
                                <input id="footerPageTitle" name="title" class="form-control" maxlength="150" required value="<?php echo footer_pages_h($form['title']); ?>" placeholder="Example: Shipping Policy">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="footerPageSlug">URL slug *</label>
                                <input id="footerPageSlug" name="slug" class="form-control" maxlength="160" required value="<?php echo footer_pages_h($form['slug']); ?>" placeholder="shipping-policy">
                                <div class="form-text">New pages open as <code>footer-page.php?slug=your-slug</code>.</div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="footerPageOrder">Footer order</label>
                                <input id="footerPageOrder" name="sort_order" class="form-control" type="number" min="0" value="<?php echo (int)$form['sort_order']; ?>">
                            </div>
                            <?php foreach ($form['styles'] as $styleKey => $styleValue): ?>
                                <input type="hidden" name="<?php echo footer_pages_h($styleKey); ?>" value="<?php echo footer_pages_h($styleValue); ?>">
                            <?php endforeach; ?>
                            <div class="col-12">
                                <?php if ($isFaqEditor): ?>
                                <label class="form-label mb-2">FAQ content</label>
                                <div class="alert alert-light border small">Each box is one FAQ section. Change a section title, question, or answer directly. Use the buttons to add or remove items.</div>
                                <div id="faqBuilder" data-source-url="<?php echo footer_pages_h($previewUrl); ?>" data-source-selector="<?php echo footer_pages_h($previewSelector); ?>"></div>
                                <button class="btn btn-outline-primary btn-sm" type="button" id="addFaqSection"><i class="fas fa-plus"></i> Add FAQ Section</button>
                                <textarea id="footerPageContent" name="content" class="form-control footer-page-content" maxlength="50000"><?php echo footer_pages_h($form['content']); ?></textarea>
                                <?php else: ?>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <label class="form-label mb-0" for="footerPageVisualEditor">Page content</label>
                                    <?php if ($editing && $form['legacy_path'] !== ''): ?><button class="btn btn-sm btn-outline-primary" type="button" id="loadCurrentSections" data-source-url="<?php echo footer_pages_h($previewUrl); ?>" data-source-selector="<?php echo footer_pages_h($previewSelector); ?>"><i class="fas fa-download"></i> Load Current Sections</button><?php endif; ?>
                                </div>
                                <div class="alert alert-light border small mb-2"><strong>How to edit:</strong> click any title or paragraph inside a dashed section and type. Click inside one paragraph or heading, then use Color or Text size to change that item.</div>
                                <div class="footer-page-editor-toolbar" aria-label="Text formatting toolbar">
                                    <select id="editorTextStyle" class="form-select form-select-sm" style="width:auto" aria-label="Text style">
                                        <option value="p">Paragraph</option>
                                        <option value="h2">Section title</option>
                                        <option value="h3">Small heading</option>
                                    </select>
                                    <label class="mb-0 small text-muted" for="editorTextColor">Color</label>
                                    <input id="editorTextColor" class="form-control form-control-color form-control-sm" type="color" value="#1683e8" title="Selected text color">
                                    <select id="editorFontSize" class="form-select form-select-sm" style="width:auto" aria-label="Selected text size">
                                        <option value="">Text size</option>
                                        <option value="12">12px</option>
                                        <option value="14">14px</option>
                                        <option value="15">15px</option>
                                        <option value="16">16px</option>
                                        <option value="18">18px</option>
                                        <option value="20">20px</option>
                                        <option value="24">24px</option>
                                    </select>
                                    <button class="btn btn-sm btn-light" type="button" data-editor-command="bold"><strong>B</strong></button>
                                    <button class="btn btn-sm btn-light" type="button" data-editor-command="italic"><em>I</em></button>
                                    <button class="btn btn-sm btn-light" type="button" data-editor-command="insertUnorderedList"><i class="fas fa-list-ul"></i> List</button>
                                    <button class="btn btn-sm btn-light" type="button" data-editor-command="createLink"><i class="fas fa-link"></i> Link</button>
                                    <button class="btn btn-sm btn-outline-secondary d-none" type="button" id="removeEditorLink"><i class="fas fa-unlink"></i> Remove link</button>
                                    <button class="btn btn-sm btn-outline-primary" type="button" id="addEditorParagraph"><i class="fas fa-plus"></i> Add Paragraph</button>
                                    <button class="btn btn-sm btn-outline-primary" type="button" id="addEditorSection" data-section-class="<?php echo footer_pages_h($sectionClass); ?>"><i class="fas fa-plus"></i> Add Section</button>
                                </div>
                                <div id="footerPageVisualEditor" class="footer-page-visual-editor" contenteditable="true" role="textbox" aria-multiline="true"><?php echo $form['content']; ?></div>
                                <textarea id="footerPageContent" name="content" class="form-control footer-page-content" maxlength="50000"><?php echo footer_pages_h($form['content']); ?></textarea>
                                <div class="form-text">Click in the page and type normally. Use the toolbar for headings, bold text, lists, and links. No HTML knowledge is required.</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-check-label"><input class="form-check-input" name="is_active" type="checkbox" value="1" <?php echo $form['is_active'] ? 'checked' : ''; ?>> Show this link in the footer</label>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save Footer Page</button>
                            <a class="btn btn-outline-secondary" href="footer_pages.php">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>
            <?php endif; ?>

            <section class="card">
                <div class="card-header fw-bold">Footer Links (<?php echo count($pages); ?>)</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Order</th><th>Title</th><th>URL</th><th>Status</th><th>Content</th><th class="text-end">Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($pages as $page): ?>
                            <tr>
                                <td><?php echo (int)$page['sort_order']; ?></td>
                                <td class="footer-page-title"><strong><?php echo footer_pages_h($page['title']); ?></strong></td>
                                <td class="footer-page-url"><a target="_blank" rel="noopener" href="../<?php echo footer_pages_h(footerPageUrl($page)); ?>"><?php echo footer_pages_h($page['legacy_path'] && empty($page['content']) ? $page['legacy_path'] : 'footer-page.php?slug=' . $page['slug']); ?></a></td>
                                <td><span class="badge <?php echo $page['is_active'] ? 'bg-success' : 'bg-secondary'; ?> footer-page-status"><?php echo $page['is_active'] ? 'Visible' : 'Hidden'; ?></span></td>
                                <td><?php echo trim((string)$page['content']) === '' ? '<span class="text-muted">Legacy page content</span>' : '<span class="text-success">Managed content</span>'; ?></td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="footer_pages.php?edit=<?php echo (int)$page['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                    <form class="d-inline" method="post" onsubmit="return confirm('Delete this footer page? Its footer link will be removed.');">
                                        <input type="hidden" name="csrf" value="<?php echo footer_pages_h($csrf); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$page['id']; ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$pages): ?><tr><td colspan="6" class="text-center text-muted py-4">No footer pages yet. Add one to show it in the footer.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>
<script>
document.getElementById('footerPageTitle')?.addEventListener('input', function () {
    const slug = document.getElementById('footerPageSlug');
    if (!slug || slug.dataset.manuallyEdited === 'true') return;
    slug.value = this.value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
});
document.getElementById('footerPageSlug')?.addEventListener('input', function () {
    this.dataset.manuallyEdited = 'true';
});
const visualEditor = document.getElementById('footerPageVisualEditor');
const contentInput = document.getElementById('footerPageContent');
let savedEditorRange = null;
function saveEditorRange() {
    const selection = window.getSelection();
    if (!visualEditor || !selection || !selection.rangeCount) return;
    const range = selection.getRangeAt(0);
    if (visualEditor.contains(range.commonAncestorContainer)) {
        savedEditorRange = range.cloneRange();
    }
}
function restoreEditorRange() {
    if (!savedEditorRange) return false;
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(savedEditorRange);
    return true;
}
function makeLinkUrl(kind, address) {
    const value = String(address || '').trim();
    if (!value) return '';
    if (kind === 'email') return 'mailto:' + value.replace(/^mailto:/i, '');
    if (kind === 'phone') return 'tel:' + value.replace(/^tel:/i, '').replace(/\s+/g, '');
    if (/^(https?:\/\/|\/|#)/i.test(value)) return value;
    return 'https://' + value;
}
function openLinkDialog() {
    if (!visualEditor) return;
    saveEditorRange();
    const selectedText = savedEditorRange ? savedEditorRange.toString().trim() : '';
    const safeSelectedText = selectedText.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    const addLink = function (kind, address, text) {
        const href = makeLinkUrl(kind, address);
        if (!href) return;
        visualEditor.focus();
        const hasSelection = restoreEditorRange() && !savedEditorRange.collapsed;
        if (hasSelection) {
            // Do not use execCommand here: it can lose the selection after a SweetAlert popup closes.
            const link = document.createElement('a');
            link.href = href;
            link.appendChild(savedEditorRange.extractContents());
            savedEditorRange.insertNode(link);
            const afterLink = document.createRange();
            afterLink.setStartAfter(link);
            afterLink.collapse(true);
            savedEditorRange = afterLink;
        } else {
            const label = String(text || '').trim() || address;
            document.execCommand('insertHTML', false, '<a href="' + href.replace(/"/g, '&quot;') + '">' + label.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</a>');
        }
    };
    if (typeof Swal === 'undefined') {
        const address = window.prompt('Website address, email address, or phone number:');
        if (address) addLink('web', address, selectedText);
        return;
    }
    Swal.fire({
        title: 'Add link',
        html: '<div class="footer-link-form">' +
            '<div><label for="linkType">Link type</label><select id="linkType" class="footer-link-field"><option value="web">Website or page</option><option value="email">Email address</option><option value="phone">Phone number</option></select></div>' +
            '<div><label for="linkAddress">Destination</label><input id="linkAddress" class="footer-link-field" placeholder="example.com or /page"></div>' +
            '<div><label for="linkText">Text visitors click</label><input id="linkText" class="footer-link-field" value="' + safeSelectedText + '" placeholder="Link text"></div>' +
            '</div>',
        showCancelButton: true,
        confirmButtonText: 'Add link',
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        customClass: { popup: 'footer-link-swal' },
        didOpen: function () {
            const type = document.getElementById('linkType');
            const address = document.getElementById('linkAddress');
            const updateLinkHint = function () {
                const hints = {
                    web: 'example.com or /page',
                    email: 'name@example.com',
                    phone: '+91 98765 43210'
                };
                address.placeholder = hints[type.value];
            };
            type.addEventListener('change', updateLinkHint);
            updateLinkHint();
            address.focus();
        },
        preConfirm: function () {
            const address = document.getElementById('linkAddress').value.trim();
            const text = document.getElementById('linkText').value.trim();
            if (!address) {
                Swal.showValidationMessage('Enter the link destination.');
                return false;
            }
            if (!selectedText && !text) {
                Swal.showValidationMessage('Enter the text visitors will click.');
                return false;
            }
            return { kind: document.getElementById('linkType').value, address: address, text: text };
        }
    }).then(function (result) {
        if (result.isConfirmed) addLink(result.value.kind, result.value.address, result.value.text);
    });
}
function removeCurrentLink() {
    if (!visualEditor || !restoreEditorRange()) {
        window.alert('Click inside the linked text first.');
        return;
    }
    const start = savedEditorRange.startContainer;
    const element = start.nodeType === Node.ELEMENT_NODE ? start : start.parentElement;
    const link = element?.closest('a');
    if (!link || !visualEditor.contains(link)) {
        window.alert('Click inside the linked text first.');
        return;
    }
    const parent = link.parentNode;
    while (link.firstChild) parent.insertBefore(link.firstChild, link);
    parent.removeChild(link);
    visualEditor.focus();
    updateRemoveLinkButton();
}
document.querySelectorAll('[data-editor-command]').forEach(function (button) {
    button.addEventListener('mousedown', function () {
        if (button.dataset.editorCommand === 'createLink') saveEditorRange();
    });
    button.addEventListener('click', function () {
        if (!visualEditor) return;
        const command = button.dataset.editorCommand;
        if (command === 'createLink') {
            openLinkDialog();
            return;
        }
        visualEditor.focus();
        const value = button.dataset.editorValue || null;
        document.execCommand(command, false, value);
    });
});
document.getElementById('removeEditorLink')?.addEventListener('mousedown', saveEditorRange);
document.getElementById('removeEditorLink')?.addEventListener('click', removeCurrentLink);
document.getElementById('editorTextStyle')?.addEventListener('change', function () {
    changeCurrentBlockTag(this.value);
});
let activeEditorBlock = null;
function rememberEditorBlock() {
    if (!visualEditor) return;
    const selection = window.getSelection();
    const node = selection?.anchorNode;
    const element = node?.nodeType === Node.ELEMENT_NODE ? node : node?.parentElement;
    const block = element?.closest('p, h1, h2, h3, h4, li');
    if (block && visualEditor.contains(block)) activeEditorBlock = block;
}
function rgbToHex(color) {
    const matches = String(color || '').match(/\d+/g);
    if (!matches || matches.length < 3) return '';
    return '#' + matches.slice(0, 3).map(function (part) {
        return Number(part).toString(16).padStart(2, '0');
    }).join('');
}
function updateEditorToolbar() {
    if (!activeEditorBlock) return;
    const styles = window.getComputedStyle(activeEditorBlock);
    const size = Math.round(parseFloat(styles.fontSize));
    const sizeSelect = document.getElementById('editorFontSize');
    if (sizeSelect && Number.isFinite(size)) {
        let option = Array.from(sizeSelect.options).find(item => item.value === String(size));
        if (!option) {
            option = new Option(size + 'px', String(size));
            option.dataset.detected = 'true';
            sizeSelect.add(option);
        }
        sizeSelect.value = String(size);
    }
    const colorInput = document.getElementById('editorTextColor');
    const color = rgbToHex(styles.color);
    if (colorInput && color) colorInput.value = color;
}
function rememberAndUpdateEditorBlock() {
    rememberEditorBlock();
    updateEditorToolbar();
    updateRemoveLinkButton();
}
function updateRemoveLinkButton() {
    const button = document.getElementById('removeEditorLink');
    if (!button || !visualEditor) return;
    const selection = window.getSelection();
    const node = selection?.anchorNode;
    const element = node?.nodeType === Node.ELEMENT_NODE ? node : node?.parentElement;
    const link = element?.closest('a');
    button.classList.toggle('d-none', !link || !visualEditor.contains(link));
}
visualEditor?.addEventListener('mouseup', rememberAndUpdateEditorBlock);
visualEditor?.addEventListener('keyup', rememberAndUpdateEditorBlock);
visualEditor?.addEventListener('focusin', rememberAndUpdateEditorBlock);
function applyCurrentBlockStyle(property, value) {
    if (!activeEditorBlock) {
        window.alert('Click inside the paragraph or heading first, then choose its color or size.');
        return;
    }
    activeEditorBlock.style[property] = value;
    updateEditorToolbar();
}
function changeCurrentBlockTag(tagName) {
    if (!activeEditorBlock) {
        window.alert('Click inside the paragraph or heading first, then choose its style.');
        return;
    }
    const tag = String(tagName || 'p').toLowerCase();
    if (activeEditorBlock.tagName.toLowerCase() === tag) return;
    const replacement = document.createElement(tag);
    Array.from(activeEditorBlock.attributes).forEach(function (attribute) {
        replacement.setAttribute(attribute.name, attribute.value);
    });
    replacement.innerHTML = activeEditorBlock.innerHTML;
    activeEditorBlock.replaceWith(replacement);
    activeEditorBlock = replacement;
    visualEditor?.focus();
    updateEditorToolbar();
}
document.getElementById('editorTextColor')?.addEventListener('input', function () {
    applyCurrentBlockStyle('color', this.value);
});
document.getElementById('editorFontSize')?.addEventListener('change', function () {
    if (this.value) applyCurrentBlockStyle('fontSize', this.value + 'px');
});
document.getElementById('addEditorParagraph')?.addEventListener('click', function () {
    if (!visualEditor) return;
    visualEditor.focus();
    document.execCommand('insertHTML', false, '<p>New paragraph — click here and type.</p>');
});
document.getElementById('addEditorSection')?.addEventListener('click', function () {
    if (!visualEditor) return;
    const section = document.createElement('div');
    section.className = this.dataset.sectionClass || 'footer-editor-section';
    section.innerHTML = '<h2>New section title</h2><p>Click here and enter the section content.</p>';
    visualEditor.appendChild(section);
    section.querySelector('h2')?.focus();
});
document.getElementById('footerPageForm')?.addEventListener('submit', function () {
    if (faqBuilder && contentInput) {
        contentInput.value = collectFaqBuilderHtml();
    } else if (visualEditor && contentInput) {
        contentInput.value = visualEditor.innerHTML;
    }
});
document.getElementById('loadCurrentSections')?.addEventListener('click', function () {
    if (!visualEditor) return;
    const button = this;
    const loadSections = function () {
        fetch(button.dataset.sourceUrl).then(response => response.text()).then(function (html) {
            const documentSource = new DOMParser().parseFromString(html, 'text/html');
            const source = documentSource.querySelector(button.dataset.sourceSelector);
            if (!source) throw new Error('Content not found');
            source.querySelector('.page-title')?.remove();
            visualEditor.innerHTML = source.innerHTML;
            visualEditor.focus();
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Sections loaded', text: 'You can now click any text and edit it.', timer: 1800, showConfirmButton: false });
            }
        }).catch(function () {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Unable to load sections', text: 'Please refresh the page and try again.' });
            } else {
                window.alert('Unable to load the current sections. Please refresh and try again.');
            }
        });
    };
    if (!visualEditor.textContent.trim()) {
        loadSections();
        return;
    }
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Load current sections?',
            text: 'Your unsaved editor changes will be replaced by the current website content.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, load sections',
            cancelButtonText: 'Keep my changes',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            focusCancel: true,
            customClass: { popup: 'footer-sections-swal' }
        }).then(function (result) {
            if (result.isConfirmed) loadSections();
        });
    } else if (window.confirm('Replace the editor content with the current website sections?')) {
        loadSections();
    }
});
const faqBuilder = document.getElementById('faqBuilder');
function faqQuestion(question, answerHtml) {
    const item = document.createElement('div');
    item.className = 'faq-builder-question';
    const questionInput = document.createElement('input');
    questionInput.className = 'form-control mb-2 faq-question-input';
    questionInput.placeholder = 'Question';
    questionInput.value = question || '';
    const answer = document.createElement('div');
    answer.className = 'faq-builder-answer faq-answer-input';
    answer.contentEditable = 'true';
    answer.innerHTML = answerHtml || '<p>Answer</p>';
    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'btn btn-sm btn-outline-danger mt-2';
    remove.innerHTML = '<i class="fas fa-trash"></i> Remove Question';
    remove.addEventListener('click', () => item.remove());
    item.append(questionInput, answer, remove);
    return item;
}
function faqSection(title, questions) {
    const section = document.createElement('section');
    section.className = 'faq-builder-section';
    const heading = document.createElement('input');
    heading.className = 'form-control fw-bold faq-section-title';
    heading.placeholder = 'FAQ section title';
    heading.value = title || '';
    const questionsWrap = document.createElement('div');
    (questions || []).forEach(question => questionsWrap.appendChild(faqQuestion(question.title, question.answer)));
    const addQuestion = document.createElement('button');
    addQuestion.type = 'button';
    addQuestion.className = 'btn btn-sm btn-outline-primary mt-3';
    addQuestion.innerHTML = '<i class="fas fa-plus"></i> Add Question';
    addQuestion.addEventListener('click', () => questionsWrap.appendChild(faqQuestion('', '')));
    const removeSection = document.createElement('button');
    removeSection.type = 'button';
    removeSection.className = 'btn btn-sm btn-outline-danger mt-3 ms-2';
    removeSection.innerHTML = '<i class="fas fa-trash"></i> Remove Section';
    removeSection.addEventListener('click', () => section.remove());
    section.append(heading, questionsWrap, addQuestion, removeSection);
    return section;
}
function renderFaqBuilder(html) {
    if (!faqBuilder) return;
    const source = document.createElement('div');
    source.innerHTML = html || '';
    const sections = source.querySelectorAll('.faq-section');
    faqBuilder.innerHTML = '';
    if (!sections.length) {
        faqBuilder.appendChild(faqSection('New FAQ Section', [{title: '', answer: ''}]));
        return;
    }
    sections.forEach(function (section) {
        const questions = Array.from(section.querySelectorAll('.faq-item')).map(function (item) {
            return { title: item.querySelector('.faq-question')?.textContent.trim() || '', answer: item.querySelector('.faq-answer')?.innerHTML || '' };
        });
        faqBuilder.appendChild(faqSection(section.querySelector('h2')?.textContent.trim() || '', questions));
    });
}
function collectFaqBuilderHtml() {
    const output = document.createElement('div');
    faqBuilder?.querySelectorAll('.faq-builder-section').forEach(function (sectionInput) {
        const section = document.createElement('div');
        section.className = 'faq-section';
        const title = document.createElement('h2');
        title.textContent = sectionInput.querySelector('.faq-section-title')?.value.trim() || 'FAQ Section';
        section.appendChild(title);
        sectionInput.querySelectorAll('.faq-builder-question').forEach(function (questionInput) {
            const item = document.createElement('div');
            item.className = 'faq-item';
            const question = document.createElement('h3');
            question.className = 'faq-question';
            question.textContent = questionInput.querySelector('.faq-question-input')?.value.trim() || 'Question';
            const answer = document.createElement('div');
            answer.className = 'faq-answer';
            answer.innerHTML = questionInput.querySelector('.faq-answer-input')?.innerHTML || '<p>Answer</p>';
            item.append(question, answer);
            section.appendChild(item);
        });
        output.appendChild(section);
    });
    return output.innerHTML;
}
document.getElementById('addFaqSection')?.addEventListener('click', function () {
    faqBuilder?.appendChild(faqSection('New FAQ Section', [{title: '', answer: ''}]));
});
if (faqBuilder) {
    if (contentInput?.value.trim()) {
        renderFaqBuilder(contentInput.value);
    } else {
        fetch(faqBuilder.dataset.sourceUrl).then(response => response.text()).then(function (html) {
            const sourceDocument = new DOMParser().parseFromString(html, 'text/html');
            const source = sourceDocument.querySelector(faqBuilder.dataset.sourceSelector);
            source?.querySelector('.page-title')?.remove();
            renderFaqBuilder(source?.innerHTML || '');
        }).catch(function () {
            renderFaqBuilder('');
        });
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>
