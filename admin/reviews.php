<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/product_reviews.php';
require_once __DIR__ . '/includes/auth-check.php';
checkAdminPermission('manage_reviews');

$pageTitle = 'Product Reviews';
$csrf = getProductReviewCsrfToken();
$error = '';
$success = $_SESSION['reviews_flash'] ?? '';
unset($_SESSION['reviews_flash']);
$returnQuery = http_build_query(array_intersect_key($_GET, array_flip(['status','search','rating','verified','product_id','date_from','date_to','page'])));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!verifyProductReviewCsrfToken($_POST['csrf'] ?? null)) throw new RuntimeException('Your session expired. Refresh and try again.');
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'save') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $savedId = saveAdminProductReview($pdo, $_POST, $id ?: null);
            $success = ($_POST['status'] ?? '') === 'approved' ? 'Review approved successfully.' : 'Review saved successfully.';
        } elseif ($action === 'status') {
            $id = (int)($_POST['id'] ?? 0);
            $status = (string)($_POST['status'] ?? '');
            setAdminProductReviewStatus($pdo, $id, $status);
            $success = $status === 'approved' ? 'Review approved successfully.' : ($status === 'spam' ? 'Review marked as spam.' : 'Review status updated successfully.');
        } elseif ($action === 'delete') {
            deleteAdminProductReview($pdo, (int)($_POST['id'] ?? 0));
            $success = 'Review deleted successfully.';
        } elseif ($action === 'bulk') {
            $ids = array_values(array_unique(array_filter(array_map('intval', (array)($_POST['ids'] ?? [])), fn($id) => $id > 0)));
            $bulkAction = (string)($_POST['bulk_action'] ?? '');
            if (!$ids) throw new RuntimeException('Select at least one review.');
            $pdo->beginTransaction();
            foreach ($ids as $id) {
                if ($bulkAction === 'delete') deleteAdminProductReview($pdo, $id);
                elseif ($bulkAction === 'verified') {
                    $stmt = $pdo->prepare('UPDATE product_reviews SET is_verified = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
                    $stmt->execute([$id]);
                } elseif (in_array($bulkAction, PRODUCT_REVIEW_STATUSES, true)) setAdminProductReviewStatus($pdo, $id, $bulkAction);
                else throw new RuntimeException('Select a valid bulk action.');
            }
            $pdo->commit();
            $success = count($ids) . ' reviews updated successfully.';
        }
        $_SESSION['reviews_flash'] = $success;
        $postQuery = [];
        parse_str((string)($_POST['return_query'] ?? ''), $postQuery);
        $safeQuery = array_intersect_key($postQuery, array_flip(['status','search','rating','verified','product_id','date_from','date_to','page']));
        header('Location: reviews.php' . ($safeQuery ? '?' . http_build_query($safeQuery) : ''));
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = $e instanceof InvalidArgumentException || $e instanceof RuntimeException ? $e->getMessage() : 'Unable to save the review. Please try again.';
        if (!($e instanceof InvalidArgumentException || $e instanceof RuntimeException)) error_log('Product review admin action failed: ' . $e->getMessage());
    }
}

$editId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
$viewId = filter_var($_GET['view'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
$editing = $editId ? getAdminProductReview($pdo, $editId) : null;
$viewing = $viewId ? getAdminProductReview($pdo, $viewId) : null;
if ($editId && !$editing) $error = 'Review not found.';
if ($viewId && !$viewing) $error = 'Review not found.';
$form = $editing ?: ['id'=>'','product_id'=>'','customer_id'=>'','rating'=>5,'review_title'=>'','review_content'=>'','seller_code'=>'','is_verified'=>0,'status'=>'pending'];
$products = $pdo->query('SELECT id, name FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$customers = $pdo->query("SELECT id, name FROM users WHERE user_role = 'customer' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$filters = [
    'search' => trim((string)($_GET['search'] ?? '')),
    'status' => in_array($_GET['status'] ?? '', PRODUCT_REVIEW_STATUSES, true) ? $_GET['status'] : '',
    'rating' => (string)($_GET['rating'] ?? ''), 'verified' => (string)($_GET['verified'] ?? ''),
    'product_id' => (string)($_GET['product_id'] ?? ''), 'date_from' => (string)($_GET['date_from'] ?? ''), 'date_to' => (string)($_GET['date_to'] ?? ''),
];
$stats = getProductReviewStats($pdo);
$page = max(1, (int)($_GET['page'] ?? 1));
$result = listAdminProductReviews($pdo, $filters, 25, ($page - 1) * 25);
$pages = max(1, (int)ceil($result['total'] / 25));
function reviews_h($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function reviews_filter_url(array $filters, int $page = 1): string {
    $query = array_filter($filters, fn($v) => $v !== '');
    if ($page > 1) $query['page'] = $page;
    return 'reviews.php' . ($query ? '?' . http_build_query($query) : '');
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reviews - Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"><link href="assets/css/admin.css" rel="stylesheet">
<style>.review-stats{display:grid;grid-template-columns:repeat(5,minmax(125px,1fr));gap:12px}.review-stat{border:1px solid #e5e9ef;border-radius:10px;padding:16px;background:#fff}.review-stat strong{display:block;font-size:1.5rem}.review-filters{display:grid;grid-template-columns:2fr repeat(5,minmax(130px,1fr)) auto;gap:10px;align-items:end}.review-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.review-form-grid .wide{grid-column:1/-1}.review-text{max-width:320px;white-space:normal;overflow-wrap:anywhere}.review-table td{vertical-align:middle}.review-actions{display:flex;gap:5px;flex-wrap:wrap}.review-status{display:inline-block;padding:4px 9px;border-radius:30px;font-size:.78rem;font-weight:600;text-transform:capitalize}.review-status-approved{background:#dff4e5;color:#176b32}.review-status-pending{background:#fff1cc;color:#795900}.review-status-rejected{background:#fce1e1;color:#922d2d}.review-status-spam{background:#e8e8eb;color:#4d4d55}@media(max-width:1100px){.review-filters{grid-template-columns:repeat(3,minmax(0,1fr))}.review-stats{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:650px){.review-filters,.review-stats,.review-form-grid{grid-template-columns:1fr 1fr}.review-form-grid .wide{grid-column:1/-1}.review-stats .review-stat:last-child{grid-column:auto}.review-heading{align-items:flex-start!important;flex-direction:column}.review-actions .btn{padding:.25rem .4rem}}</style>
</head><body><div class="everythingb2c-admin-container"><?php include 'includes/sidebar.php'; ?><div class="everythingb2c-main-content"><?php include 'includes/header.php'; ?><main class="container-fluid p-3 p-lg-4">
<?php if ($error): ?><div class="alert alert-danger"><?php echo reviews_h($error); ?></div><?php endif; ?><?php if ($success): ?><div class="alert alert-success"><?php echo reviews_h($success); ?></div><?php endif; ?>
<div class="review-stats mb-4"><?php foreach (['total'=>'Total Reviews','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','spam'=>'Spam'] as $key=>$label): ?><div class="review-stat"><span class="text-muted"><?php echo reviews_h($label); ?></span><strong><?php echo (int)$stats[$key]; ?></strong></div><?php endforeach; ?></div>
<div class="d-flex justify-content-between review-heading mb-3"><h5 class="mb-0">Reviews <small class="text-muted">(<?php echo (int)$result['total']; ?>)</small></h5><a class="btn btn-primary" href="reviews.php?create=1"><i class="fas fa-plus"></i> Add Review</a></div>
<ul class="nav nav-tabs mb-3"><li class="nav-item"><a class="nav-link <?php echo $filters['status']===''?'active':''; ?>" href="reviews.php">All Reviews</a></li><?php foreach (PRODUCT_REVIEW_STATUSES as $status): ?><li class="nav-item"><a class="nav-link <?php echo $filters['status']===$status?'active':''; ?>" href="<?php echo reviews_h(reviews_filter_url(array_merge($filters,['status'=>$status]))); ?>"><?php echo reviews_h(ucfirst($status)); ?></a></li><?php endforeach; ?></ul>
<form class="card card-body mb-3" method="get"><div class="review-filters"><div><label class="form-label">Search</label><input name="search" class="form-control" value="<?php echo reviews_h($filters['search']); ?>" placeholder="Product, customer, review, Seller Code"></div><div><label class="form-label">Rating</label><select name="rating" class="form-select"><option value="">Any rating</option><?php for($i=5;$i>=1;$i--): ?><option value="<?php echo $i; ?>" <?php echo $filters['rating']===(string)$i?'selected':''; ?>><?php echo $i; ?> stars</option><?php endfor; ?></select></div><div><label class="form-label">Verified</label><select name="verified" class="form-select"><option value="">Any</option><option value="1" <?php echo $filters['verified']==='1'?'selected':''; ?>>Verified</option><option value="0" <?php echo $filters['verified']==='0'?'selected':''; ?>>Not verified</option></select></div><div><label class="form-label">Product</label><select name="product_id" class="form-select"><option value="">All products</option><?php foreach($products as $p): ?><option value="<?php echo (int)$p['id']; ?>" <?php echo $filters['product_id']===(string)$p['id']?'selected':''; ?>><?php echo reviews_h($p['name']); ?></option><?php endforeach; ?></select></div><div><label class="form-label">From</label><input type="date" name="date_from" class="form-control" value="<?php echo reviews_h($filters['date_from']); ?>"></div><div><label class="form-label">To</label><input type="date" name="date_to" class="form-control" value="<?php echo reviews_h($filters['date_to']); ?>"></div><div class="d-flex gap-2"><button class="btn btn-primary">Filter</button><a class="btn btn-outline-secondary" href="reviews.php">Reset</a></div></div><?php if($filters['status']): ?><input type="hidden" name="status" value="<?php echo reviews_h($filters['status']); ?>"><?php endif; ?></form>
<?php if (isset($_GET['create']) || $editing): ?><div class="card mb-3"><div class="card-header fw-bold"><?php echo $editing?'Edit Review':'Add Review'; ?></div><div class="card-body"><form method="post"><input type="hidden" name="csrf" value="<?php echo reviews_h($csrf); ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?php echo reviews_h($form['id']); ?>"><div class="review-form-grid">
<div><label class="form-label">Product *</label><select class="form-select" name="product_id" required><option value="">Select product</option><?php foreach($products as $p): ?><option value="<?php echo (int)$p['id']; ?>" <?php echo (string)$form['product_id']===(string)$p['id']?'selected':''; ?>><?php echo reviews_h($p['name']); ?></option><?php endforeach; ?></select></div>
<div><label class="form-label">Customer *</label><select class="form-select" name="customer_id" required><option value="">Select customer</option><?php foreach($customers as $c): ?><option value="<?php echo (int)$c['id']; ?>" <?php echo (string)$form['customer_id']===(string)$c['id']?'selected':''; ?>><?php echo reviews_h($c['name']); ?></option><?php endforeach; ?></select></div>
<div><label class="form-label">Rating *</label><select class="form-select" name="rating" required><?php for($i=1;$i<=5;$i++): ?><option value="<?php echo $i; ?>" <?php echo (int)$form['rating']===$i?'selected':''; ?>><?php echo $i; ?> star<?php echo $i>1?'s':''; ?></option><?php endfor; ?></select></div>
<div><label class="form-label">Status</label><select class="form-select" name="status"><?php foreach(PRODUCT_REVIEW_STATUSES as $s): ?><option value="<?php echo $s; ?>" <?php echo $form['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option><?php endforeach; ?></select></div>
<div class="wide"><label class="form-label">Review Title *</label><input class="form-control" name="review_title" maxlength="200" required value="<?php echo reviews_h($form['review_title']); ?>"></div><div class="wide"><label class="form-label">Review Content *</label><textarea class="form-control" name="review_content" rows="4" maxlength="10000" required><?php echo reviews_h($form['review_content']); ?></textarea></div>
<div><label class="form-label">Seller Code *</label><input class="form-control" name="seller_code" maxlength="40" pattern="[A-Za-z0-9][A-Za-z0-9_-]{0,39}" value="<?php echo reviews_h($form['seller_code']); ?>" placeholder="Enter manually (required to approve)"><small class="text-muted">Enter the code manually. Required before publishing.</small></div><div class="d-flex align-items-center pt-4"><label><input type="checkbox" name="is_verified" value="1" <?php echo !empty($form['is_verified'])?'checked':''; ?>> Verified Purchase</label></div>
</div><div class="mt-3 d-flex gap-2"><button class="btn btn-primary" type="submit">Save Changes</button><button class="btn btn-success" type="submit" onclick="this.form.status.value='approved'">Approve &amp; Publish</button><a class="btn btn-outline-secondary" href="reviews.php">Cancel</a></div></form></div></div><?php endif; ?>
<?php if($viewing): ?><div class="alert alert-info"><strong>Review details:</strong> <?php echo reviews_h($viewing['product_name']); ?> · <?php echo reviews_h($viewing['customer_name'] ?: 'Deleted customer'); ?> · <?php echo (int)$viewing['rating']; ?>/5<br><strong><?php echo reviews_h($viewing['review_title']); ?></strong><br><?php echo nl2br(reviews_h($viewing['review_content'])); ?><br>Seller Code: <?php echo reviews_h($viewing['seller_code'] ?? 'Not entered'); ?> · <?php echo !empty($viewing['is_verified'])?'Verified Purchase':'Not verified'; ?><br><a href="reviews.php?edit=<?php echo (int)$viewing['id']; ?>">Edit review</a></div><?php endif; ?>
<form method="post" id="bulkReviews"><input type="hidden" name="csrf" value="<?php echo reviews_h($csrf); ?>"><input type="hidden" name="action" value="bulk"><input type="hidden" name="return_query" value="<?php echo reviews_h($returnQuery); ?>"><div class="d-flex gap-2 mb-2"><select class="form-select" style="max-width:220px" name="bulk_action" required><option value="">Bulk action</option><option value="approved">Approve</option><option value="rejected">Reject</option><option value="spam">Mark as Spam</option><option value="delete">Delete</option></select><button class="btn btn-outline-primary" type="submit" onclick="return confirm('Apply this action to selected reviews?')">Apply</button></div>
<div class="card"><div class="table-responsive"><table class="table table-hover review-table mb-0"><thead class="table-light"><tr><th><input type="checkbox" id="selectAll" aria-label="Select all"></th><th>Product</th><th>Customer</th><th>Rating</th><th>Review</th><th>Seller Code</th><th>Verified</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>
<?php foreach($result['reviews'] as $r): ?><tr><td><input type="checkbox" name="ids[]" value="<?php echo (int)$r['id']; ?>" class="review-check"></td><td><?php echo reviews_h($r['product_name'] ?: 'Deleted product'); ?></td><td><?php echo reviews_h($r['customer_name'] ?: 'Deleted customer'); ?></td><td><?php echo str_repeat('★',(int)$r['rating']).str_repeat('☆',5-(int)$r['rating']); ?> <small><?php echo (int)$r['rating']; ?>/5</small></td><td class="review-text"><strong><?php echo reviews_h($r['review_title']); ?></strong><br><?php echo reviews_h(mb_strimwidth($r['review_content'],0,150,'…')); ?></td><td><?php echo reviews_h($r['seller_code'] ?: '—'); ?></td><td><?php echo !empty($r['is_verified'])?'<span class="text-success">Yes</span>':'No'; ?></td><td><span class="review-status review-status-<?php echo reviews_h($r['status']); ?>"><?php echo reviews_h($r['status']); ?></span></td><td><?php echo reviews_h(date('Y-m-d',strtotime($r['created_at']))); ?></td><td><div class="review-actions"><a class="btn btn-sm btn-outline-secondary" title="View" href="reviews.php?view=<?php echo (int)$r['id']; ?>"><i class="fas fa-eye"></i></a><a class="btn btn-sm btn-outline-primary" title="Edit" href="reviews.php?edit=<?php echo (int)$r['id']; ?>"><i class="fas fa-edit"></i></a><?php if($r['status']!=='approved'): ?><button class="btn btn-sm btn-success" title="Approve" value="approved" onclick="return submitRowAction(this,<?php echo (int)$r['id']; ?>,'status')"><i class="fas fa-check"></i></button><?php endif; ?><?php if($r['status']!=='rejected'): ?><button class="btn btn-sm btn-secondary" title="Reject" value="rejected" onclick="return submitRowAction(this,<?php echo (int)$r['id']; ?>,'status')"><i class="fas fa-times"></i></button><?php endif; ?><button class="btn btn-sm btn-warning" title="Mark as Spam" value="spam" onclick="return submitRowAction(this,<?php echo (int)$r['id']; ?>,'status')"><i class="fas fa-ban"></i></button><button class="btn btn-sm btn-danger" title="Delete" onclick="return submitRowAction(this,<?php echo (int)$r['id']; ?>,'delete')"><i class="fas fa-trash"></i></button></div></td></tr><?php endforeach; ?>
<?php if(!$result['reviews']): ?><tr><td colspan="10" class="text-center text-muted py-4">No reviews match these filters.</td></tr><?php endif; ?></tbody></table></div></div></form>
<?php if($pages>1): ?><nav class="mt-3"><ul class="pagination justify-content-center"><?php for($p=1;$p<=$pages;$p++): ?><li class="page-item <?php echo $p===$page?'active':''; ?>"><a class="page-link" href="<?php echo reviews_h(reviews_filter_url($filters,$p)); ?>"><?php echo $p; ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
</main></div></div>
<form id="rowAction" method="post" hidden><input type="hidden" name="csrf" value="<?php echo reviews_h($csrf); ?>"><input type="hidden" name="id"><input type="hidden" name="action"><input type="hidden" name="status"></form>
<script>document.getElementById('selectAll').addEventListener('change',e=>document.querySelectorAll('.review-check').forEach(c=>c.checked=e.target.checked));function submitRowAction(btn,id,action){if(action==='delete'&&!confirm('Delete this review permanently?'))return false;const f=document.getElementById('rowAction');f.elements.id.value=id;f.elements.action.value=action;f.elements.status.value=btn.value||'';f.submit();return false;}</script><script src="assets/js/admin.js"></script></body></html>
