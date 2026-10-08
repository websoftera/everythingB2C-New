<?php

require_once __DIR__ . '/../database/footer_pages_migration.php';

function ensureFooterPagesSchema(PDO $pdo): bool
{
    static $ready = null;
    if ($ready !== null) {
        return $ready;
    }

    try {
        migrateFooterPagesSchema($pdo);
        $ready = true;
    } catch (Throwable $e) {
        error_log('Footer pages migration failed: ' . $e->getMessage());
        $ready = false;
    }

    return $ready;
}

function footerPageSlug(string $value): string
{
    $slug = strtolower(trim($value));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim((string)$slug, '-');
}

function getActiveFooterPages(PDO $pdo): array
{
    if (!ensureFooterPagesSchema($pdo)) {
        return [];
    }

    $stmt = $pdo->query('SELECT id, title, slug, legacy_path, sort_order
        FROM footer_pages WHERE is_active = 1 ORDER BY sort_order, id');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFooterPageBySlug(PDO $pdo, string $slug, bool $activeOnly = true): ?array
{
    if (!ensureFooterPagesSchema($pdo)) {
        return null;
    }

    $sql = 'SELECT * FROM footer_pages WHERE slug = ?' . ($activeOnly ? ' AND is_active = 1' : '') . ' LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$slug]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
    return $page ?: null;
}

function getFooterPageByLegacyPath(PDO $pdo, string $legacyPath): ?array
{
    if (!ensureFooterPagesSchema($pdo)) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT * FROM footer_pages WHERE legacy_path = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$legacyPath]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
    return $page ?: null;
}

function footerPageUrl(array $page, string $baseUrl = ''): string
{
    if (!empty($page['legacy_path']) && empty($page['content'])) {
        return $baseUrl . $page['legacy_path'];
    }

    return $baseUrl . 'footer-page.php?slug=' . rawurlencode((string)$page['slug']);
}

function footerPageContentClass(array $page): string
{
    $classes = [
        'about.php' => 'about-us-content',
        'returns.php' => 'returns-policy-content',
        'privacy.php' => 'privacy-policy-content',
        'faq.php' => 'faq-content',
    ];

    return $classes[(string)($page['legacy_path'] ?? '')] ?? 'footer-custom-content';
}

function footerPageStyleSettings(array $page): array
{
    $defaults = [
        'title_color' => '#333333',
        'subtitle_color' => '#2c539f',
        'description_color' => '#666666',
        'bullet_color' => '#1683e8',
        'title_size' => 32,
        'subtitle_size' => 24,
        'description_size' => 15,
    ];
    $saved = json_decode((string)($page['style_settings'] ?? ''), true);
    if (!is_array($saved)) {
        return $defaults;
    }
    foreach ($defaults as $key => $default) {
        if (!array_key_exists($key, $saved)) {
            $saved[$key] = $default;
        }
    }
    return $saved;
}

function footerPageStyleVariables(array $page): string
{
    $settings = footerPageStyleSettings($page);
    $colorKeys = ['title_color', 'subtitle_color', 'description_color', 'bullet_color'];
    $sizeKeys = ['title_size', 'subtitle_size', 'description_size'];
    $variables = [];
    foreach ($colorKeys as $key) {
        $value = preg_match('/^#[0-9a-f]{6}$/i', (string)$settings[$key]) ? $settings[$key] : footerPageStyleSettings([])[$key];
        $variables[] = '--footer-' . str_replace('_color', '', $key) . ':' . $value;
    }
    foreach ($sizeKeys as $key) {
        $value = max(10, min(52, (int)$settings[$key]));
        $variables[] = '--footer-' . str_replace('_size', '', $key) . '-size:' . $value . 'px';
    }
    return implode(';', $variables);
}

function sanitizeFooterPageContent(string $content): string
{
    $content = trim($content);
    $content = strip_tags($content, '<p><br><strong><b><em><i><h1><h2><h3><h4><ul><ol><li><a><div><span><blockquote><hr>');
    $content = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $content);
    $content = preg_replace_callback('/\s+style\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', function (array $match): string {
        $style = html_entity_decode($match[2] ?? $match[3] ?? $match[4] ?? '', ENT_QUOTES, 'UTF-8');
        $allowed = [];
        if (preg_match('/(?:^|;)\s*color\s*:\s*(#[0-9a-f]{6})\s*(?:;|$)/i', $style, $color)) {
            $allowed[] = 'color:' . strtolower($color[1]);
        }
        if (preg_match('/(?:^|;)\s*font-size\s*:\s*(1[0-9]|2[0-4]|[8-9])px\s*(?:;|$)/i', $style, $size)) {
            $allowed[] = 'font-size:' . $size[1] . 'px';
        }
        return $allowed ? ' style="' . implode(';', $allowed) . '"' : '';
    }, $content);
    $content = preg_replace_callback('/\s+href\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', function (array $match): string {
        $href = html_entity_decode($match[2] ?? $match[3] ?? $match[4] ?? '', ENT_QUOTES, 'UTF-8');
        if (!preg_match('#^(https?://|mailto:|tel:|/|#)#i', $href)) {
            return '';
        }
        return ' href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"';
    }, $content);
    return $content;
}

function footerPageLegacyLayout(array $page): ?array
{
    $layouts = [
        'about.php' => ['class' => 'about-us-content', 'column' => 'col-lg-10'],
        'returns.php' => ['class' => 'returns-policy-content', 'column' => 'col-lg-8'],
        'privacy.php' => ['class' => 'privacy-policy-content', 'column' => 'col-lg-8'],
        'faq.php' => ['class' => 'faq-content', 'column' => 'col-lg-8'],
    ];

    return $layouts[(string)($page['legacy_path'] ?? '')] ?? null;
}

function footerPageLegacyStyles(array $page): string
{
    $layout = footerPageLegacyLayout($page);
    $legacyPath = (string)($page['legacy_path'] ?? '');
    if (!$layout || !in_array($legacyPath, ['about.php', 'returns.php', 'privacy.php', 'faq.php'], true)) {
        return '';
    }

    $source = @file_get_contents(__DIR__ . '/../' . $legacyPath);
    if ($source === false || !preg_match_all('/<style>(.*?)<\\/style>/si', $source, $matches)) {
        return '';
    }

    return (string)end($matches[1]);
}

function footerPageDefaultStyles(): string
{
    return footerPageLegacyStyles(['legacy_path' => 'privacy.php']);
}

function renderFooterManagedPage(array $page): void
{
    $pageTitle = (string)$page['title'];
    $layout = footerPageLegacyLayout($page);

    if ($layout) {
        ?>
        <div class="container mt-4">
            <div class="row">
                <div class="<?php echo htmlspecialchars($layout['column'], ENT_QUOTES, 'UTF-8'); ?> mx-auto">
                    <div class="<?php echo htmlspecialchars($layout['class'], ENT_QUOTES, 'UTF-8'); ?> footer-page-custom-styles" style="<?php echo htmlspecialchars(footerPageStyleVariables($page), ENT_QUOTES, 'UTF-8'); ?>">
                        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <?php echo $page['content']; ?>
                    </div>
                </div>
            </div>
        </div>
        <style><?php echo footerPageLegacyStyles($page); ?></style>
        <?php
        return;
    }
    ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="privacy-policy-content footer-custom-content footer-page-custom-styles" style="<?php echo htmlspecialchars(footerPageStyleVariables($page), ENT_QUOTES, 'UTF-8'); ?>">
                    <h1 class="page-title"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <div class="footer-managed-page-content privacy-section"><?php echo $page['content']; ?></div>
                </div>
            </div>
        </div>
    </div>
    <style>
        <?php echo footerPageDefaultStyles(); ?>
        .footer-page-custom-styles .page-title { color:var(--footer-title); font-size:var(--footer-title-size); }
        .footer-page-custom-styles h2,
        .footer-page-custom-styles h3 { color:var(--footer-subtitle); font-size:var(--footer-subtitle-size); }
        .footer-page-custom-styles h2 {
            border-left:4px solid var(--site-blue);
            padding-left:15px;
        }
        .footer-page-custom-styles p,
        .footer-page-custom-styles li { color:var(--footer-description); font-size:var(--footer-description-size); }
        .footer-page-custom-styles li::marker,
        .footer-page-custom-styles li::before { color:var(--footer-bullet) !important; }
        .footer-custom-content .footer-managed-page-content > h2,
        .footer-custom-content .footer-managed-page-content > h3 {
            border-left:0;
            color:#161616;
            font-size:18px;
            font-weight:600;
            margin:0 0 20px;
            padding-left:0;
        }
        .footer-custom-content .footer-editor-section h2 {
            border-left:4px solid var(--site-blue);
            color:var(--footer-subtitle);
            font-size:var(--footer-subtitle-size);
            font-weight:600;
            margin:0 0 20px;
            padding-left:15px;
        }
        .footer-custom-content .footer-managed-page-content > p,
        .footer-custom-content .footer-managed-page-content > ul,
        .footer-custom-content .footer-managed-page-content > ol,
        .footer-custom-content .footer-managed-page-content,
        .footer-custom-content .footer-managed-page-content p,
        .footer-custom-content .footer-managed-page-content li,
        .footer-custom-content .footer-managed-page-content div {
            color:#161616;
        }
        .footer-custom-content .footer-managed-page-content > div { margin-bottom:15px; }
        .footer-custom-content .footer-editor-section {
            border-bottom:1px solid #e9ecef;
            margin-bottom:35px;
            padding-bottom:20px;
        }
        .footer-custom-content a {
            color:var(--site-blue);
            font-weight:500;
            text-decoration:none;
        }
        .footer-custom-content a:hover { color:var(--dark-blue); text-decoration:underline; }
        @media (max-width:768px) {
            .footer-custom-content .footer-managed-page-content > p,
            .footer-custom-content .footer-managed-page-content > ul,
            .footer-custom-content .footer-managed-page-content > ol,
            .footer-custom-content .footer-managed-page-content > div { font-size:12px; }
            .footer-custom-content .footer-managed-page-content > h2,
            .footer-custom-content .footer-managed-page-content > h3 { font-size:15px !important; }
            .footer-custom-content .footer-editor-section h2 { font-size:var(--footer-subtitle-size) !important; }
        }
    </style>
    <?php
}

function renderManagedFooterPageForLegacyPath(PDO $pdo, string $legacyPath): bool
{
    $page = getFooterPageByLegacyPath($pdo, $legacyPath);
    if (!$page || trim((string)$page['content']) === '') {
        return false;
    }

    include __DIR__ . '/header.php';
    echo renderBreadcrumb(generateBreadcrumb((string)$page['title']));
    renderFooterManagedPage($page);
    include __DIR__ . '/footer.php';
    return true;
}
