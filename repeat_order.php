<?php
session_start();
require_once 'includes/functions.php';
if (!isLoggedIn()) { $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; header('Location: login.php'); exit; }
$userId = (int)$_SESSION['user_id'];
$message = '';
if (isset($_GET['order_id'])) {
    $result = startRepeatOrder($userId, (int)$_GET['order_id']);
    if (!$result['success']) $message = $result['message'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selections = $_POST['items'] ?? [];
    $removeItemKey = (string)($_POST['remove_item'] ?? '');
    if ($removeItemKey !== '') unset($selections[$removeItemKey]);
    $extraProductId = (int)($_POST['extra_product_id'] ?? 0);
    $extraQuantity = max(1, (int)($_POST['extra_quantity'] ?? 1));
    if ($extraProductId > 0) $selections[(string)$extraProductId] = $extraQuantity;
    saveRepeatOrderSelections($selections);
    if (isset($_POST['checkout']) && !empty(getRepeatOrderSessionItems())) { header('Location: checkout.php?repeat_order=1'); exit; }
    $message = $removeItemKey !== '' ? 'Product removed from this repeat order.' : 'Your repeat-order selection was updated.';
}
$items = getRepeatOrderSessionItems();
$sourceOrderId = (int)($_SESSION['repeat_order']['source_order_id'] ?? 0);
$extraProducts = [];
if ($sourceOrderId) {
    $extraProducts = $pdo->query("SELECT id, name, selling_price FROM products WHERE stock_quantity > 0 ORDER BY name ASC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
}
$extraProductSearchData = array_map(function ($product) {
    return ['id' => (int)$product['id'], 'name' => cleanProductName($product['name']), 'price' => number_format((float)$product['selling_price'], 2)];
}, $extraProducts);
$pageTitle = 'Repeat Order'; include 'includes/header.php';
?>
<style>
  body { background:linear-gradient(135deg,#f8fbff 0%,#fff 55%,#f5fbff 100%); }
  .repeat-order-page { display:block !important; max-width:1180px; margin:34px auto 60px; padding:0 18px; color:#12204a; font-family:'Mulish',sans-serif !important; }
  .repeat-order-page *, .repeat-order-page *::before, .repeat-order-page *::after { box-sizing:border-box; }
  .ro-heading { display:flex; align-items:center; justify-content:space-between; gap:20px; margin-bottom:30px; }.ro-heading-copy { display:flex; align-items:center; gap:17px; }
  .ro-heading-icon,.ro-add-icon { display:inline-flex; align-items:center; justify-content:center; flex:0 0 auto; width:54px; height:54px; color:#1169ff; background:#eaf3ff; border-radius:50%; font-size:22px; }.ro-heading h1 { margin:0 0 4px; font-size:23px; font-weight:700; letter-spacing:0; } .ro-heading p { margin:0; color:#6179a5; font-size:14px; }
  .ro-back { border:1px solid #b9cbe9; border-radius:8px; padding:12px 18px; text-decoration:none; color:#1d315b; font-size:14px; font-weight:700; white-space:nowrap; }.ro-back:hover { background:#edf5ff; color:#1d315b; }
  .ro-panel { display:block; background:#fff; border:1px solid #d8e5f7; border-radius:10px; overflow:hidden; box-shadow:0 6px 22px rgba(45,94,160,.05); margin:0; padding:0 !important; }
  .ro-panel-title { padding:19px 28px; background:linear-gradient(90deg,#fbfdff,#f4f9ff); border-bottom:1px solid #edf2f8; font-size:18px; font-weight:800; }
  .ro-item { display:grid; grid-template-columns:82px minmax(0,1fr) 120px 155px 30px; gap:14px; align-items:center; margin:0 20px; padding:16px 8px; border-bottom:1px solid #e3ebf5; }
  .ro-item:last-child { border-bottom:0; } .ro-image { width:82px; height:82px; border:1px solid #d7e4f4; border-radius:11px; object-fit:contain; padding:5px; }
  .ro-name { min-width:0; overflow-wrap:anywhere; color:#12204a; font-size:16px; font-weight:800; line-height:1.35; } .ro-meta { color:#6881ad; margin-top:7px; font-size:14px; } .ro-price { color:#12204a; font-size:16px; font-weight:800; text-align:right; white-space:nowrap; }
  .ro-qty-label { text-align:left; color:#5e75a0; font-size:12px; margin-bottom:7px; font-weight:700; } .ro-qty { display:flex; align-items:center; }
  .ro-qty button { width:39px; height:44px; border:1px solid #dce7f6; background:#f9fbfe; color:#0869ff; font-size:23px; line-height:1; cursor:pointer; } .ro-qty button:first-child { border-radius:9px 0 0 9px; } .ro-qty button:last-child { border-radius:0 9px 9px 0; }
  .ro-qty input { width:70px; height:44px; border:1px solid #dce7f6; border-left:0; border-right:0; text-align:center; color:#12204a; background:#fff; font-size:15px; font-weight:800; outline:0; -moz-appearance:textfield; } .ro-qty input::-webkit-outer-spin-button, .ro-qty input::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
  .ro-remove { align-self:start; width:30px; height:30px; margin-top:4px; padding:0; border:0; border-radius:50%; background:transparent; color:#8aa0c2; font-size:21px; line-height:1; cursor:pointer; } .ro-remove:hover { background:#fff0f0; color:#dc3545; }
  .ro-extra { padding:18px 22px; margin:0; display:flex; align-items:center; gap:22px; border-top:1px solid #e3ebf5; } .ro-extra h2 { display:flex; align-items:center; margin:0; min-width:285px; color:#12204a; font-size:16px; font-weight:700; } .ro-extra-title { display:block; } .ro-extra-caption { display:block; margin-top:5px; color:#6881ad; font-size:13px; font-weight:400; } .ro-extra h2 .ro-add-icon { display:inline-flex; width:40px; height:40px; margin:0 13px 0 0; font-size:20px; } .ro-extra-row { display:grid; grid-template-columns:minmax(250px,1fr) 104px 138px; gap:18px; flex:1; } .ro-extra input { min-height:46px; border:1px solid #d3e1f3; border-radius:8px; padding:8px 14px; color:#425b88; background:#fff; font-size:14px; }
  .ro-product-search { position:relative; } .ro-product-search input { width:100%; } .ro-search-results { display:none; position:absolute; z-index:20; top:calc(100% + 4px); left:0; right:0; max-height:225px; overflow-y:auto; background:#fff; border:1px solid #d3e1f3; border-radius:8px; box-shadow:0 8px 20px rgba(27,67,124,.15); } .ro-search-results.is-open { display:block; } .ro-search-option { display:block; width:100%; padding:10px 13px; border:0; border-bottom:1px solid #edf2f8; color:#1d315b; background:#fff; text-align:left; font:inherit; font-size:13px; cursor:pointer; } .ro-search-option:last-child { border-bottom:0; } .ro-search-option:hover { background:#eff6ff; } .ro-search-price { float:right; color:#6881ad; }
  .ro-actions { display:flex; justify-content:flex-end; gap:18px; padding:14px 22px 20px; border-top:1px solid #e3ebf5; } .ro-btn { min-width:180px; min-height:49px; border-radius:8px; padding:10px 18px; font-size:14px; font-weight:800; cursor:pointer; } .ro-update { background:#fff; border:1px solid #1169ff; color:#1169ff; } .ro-checkout { background:linear-gradient(90deg,#85c833,#58ad32); border:1px solid #71bd31; color:#fff; } .ro-add { min-width:0; background:linear-gradient(90deg,#126aff,#2463ed); border:1px solid #126aff; color:#fff; }
  .ro-note { margin:0 22px 20px; padding:12px 16px; background:#edf7ff; border:1px solid #d2e8ff; border-radius:7px; color:#6881ad; font-size:13px; } .ro-empty { padding:32px 20px; text-align:center; color:#6881ad; }
  @media (max-width:700px) { .repeat-order-page { margin-top:22px; padding:0 12px; } .ro-heading { align-items:flex-start; } .ro-heading h1 { font-size:22px; } .ro-heading-icon { width:45px; height:45px; font-size:18px; } .ro-item { grid-template-columns:58px minmax(0,1fr) 108px 25px; gap:8px; margin:0 10px; padding:14px 2px; } .ro-image { width:54px; height:54px; } .ro-price { grid-column:2; text-align:left; } .ro-qty-wrap { grid-column:3; grid-row:1 / span 2; } .ro-remove { grid-column:4; grid-row:1; } .ro-qty input { width:36px; } .ro-qty button { width:29px; } .ro-extra { display:block; } .ro-extra h2 { min-width:0; margin-bottom:12px; } .ro-extra-row { grid-template-columns:1fr 65px 70px; gap:7px; } .ro-actions { flex-direction:column-reverse; gap:8px; padding:14px; } .ro-btn { width:100%; } .ro-note { margin:0 14px 14px; } }
</style>
<main class="repeat-order-page">
  <div class="ro-heading"><div class="ro-heading-copy"><span class="ro-heading-icon"><i class="fas fa-repeat"></i></span><div><h1>Repeat order</h1><p>Review your earlier items and adjust quantities before checkout.</p></div></div><a class="ro-back" href="myaccount.php?tab=orders#orders"><i class="fas fa-arrow-left me-2"></i> Back to orders</a></div>
  <?php if ($message): ?><div class="alert alert-warning"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <?php if (!$sourceOrderId): ?><div class="ro-panel ro-empty">Choose <strong>Repeat Order</strong> on one of your previous orders first.</div>
  <?php else: ?><form method="post" id="repeatOrderForm"><section class="ro-panel"><div class="ro-panel-title">Your selected products</div>
    <?php if ($items): foreach ($items as $item): $key = $item['product_id'] . (!empty($item['variation_id']) ? ':' . $item['variation_id'] : ''); $step = max(1, (int)$item['package_quantity']); ?>
      <article class="ro-item"><img class="ro-image" src="<?= htmlspecialchars($item['main_image'] ?: './uploads/products/blank-img.webp') ?>" alt="" onerror="this.src='./uploads/products/blank-img.webp'"><div><div class="ro-name"><?= htmlspecialchars($item['name']) ?></div><div class="ro-meta">Current price · ₹<?= number_format($item['selling_price'], 2) ?> each</div></div><div class="ro-price">₹<?= number_format($item['selling_price'] * getCartItemPriceMultiplier($item), 2) ?></div><div class="ro-qty-wrap"><div class="ro-qty-label">QUANTITY</div><div class="ro-qty"><button type="button" class="ro-qty-change" data-change="-<?= $step ?>" aria-label="Decrease quantity">−</button><input class="ro-qty-input" name="items[<?= htmlspecialchars($key) ?>]" value="<?= (int)$item['quantity'] ?>" min="0" step="<?= $step ?>" inputmode="numeric"><button type="button" class="ro-qty-change" data-change="<?= $step ?>" aria-label="Increase quantity">+</button></div></div><button type="submit" class="ro-remove" name="remove_item" value="<?= htmlspecialchars($key) ?>" title="Remove product" aria-label="Remove <?= htmlspecialchars($item['name']) ?>">×</button></article>
    <?php endforeach; else: ?><div class="ro-empty">Those original products are no longer available. You can still add another available product below.</div><?php endif; ?>
  <section class="ro-extra"><h2><span class="ro-add-icon"><i class="fas fa-plus"></i></span><span class="ro-extra-title">Add another product<span class="ro-extra-caption">Optional · cart stays unchanged</span></span></h2><div class="ro-extra-row"><div class="ro-product-search"><input id="extra_product_search" type="search" placeholder="Search a product" autocomplete="off" aria-label="Search a product"><input type="hidden" name="extra_product_id" id="extra_product_id"><div id="extra_product_results" class="ro-search-results" role="listbox"></div></div><input type="number" name="extra_quantity" min="1" value="1" inputmode="numeric" aria-label="Extra product quantity"><button class="ro-btn ro-add" type="submit">Add</button></div></section>
  <div class="ro-actions"><button class="ro-btn ro-update" type="submit"><i class="fas fa-sync-alt me-2"></i> Update selection</button><button class="ro-btn ro-checkout" type="submit" name="checkout" value="1"><i class="fas fa-shopping-cart me-2"></i> Proceed to checkout</button></div><div class="ro-note"><i class="fas fa-info-circle me-2"></i> Prices and stock are checked again at checkout. Use the × icon to remove an item.</div></section></form><?php endif; ?>
</main>
<script>
document.querySelectorAll('.ro-qty-change').forEach(function (button) { button.addEventListener('click', function () { var input = this.parentElement.querySelector('.ro-qty-input'); var step = Number(input.step || 1); var next = Math.max(0, Number(input.value || 0) + Number(this.dataset.change)); input.value = Math.round(next / step) * step; }); });
document.querySelectorAll('.ro-qty-input').forEach(function (input) { input.addEventListener('change', function () { var step = Number(this.step || 1), value = Math.max(0, Number(this.value || 0)); this.value = Math.round(value / step) * step; }); });
var extraProducts = <?= json_encode($extraProductSearchData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
var extraSearch = document.getElementById('extra_product_search'), extraProductId = document.getElementById('extra_product_id'), extraResults = document.getElementById('extra_product_results');
if (extraSearch && extraResults) {
  var escapeHtml = function (text) { return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); };
  var drawExtraProducts = function () { var term = extraSearch.value.toLowerCase().trim(), matches = extraProducts.filter(function (product) { return product.name.toLowerCase().indexOf(term) !== -1; }).slice(0, 12); extraResults.innerHTML = matches.length ? matches.map(function (product) { return '<button type="button" class="ro-search-option" data-id="' + product.id + '" data-name="' + escapeHtml(product.name) + '">' + escapeHtml(product.name) + '<span class="ro-search-price">₹' + product.price + '</span></button>'; }).join('') : '<div class="ro-search-option">No matching product</div>'; extraResults.classList.add('is-open'); };
  extraSearch.addEventListener('focus', drawExtraProducts); extraSearch.addEventListener('input', function () { extraProductId.value = ''; drawExtraProducts(); });
  extraResults.addEventListener('click', function (event) { var option = event.target.closest('.ro-search-option[data-id]'); if (!option) return; extraProductId.value = option.dataset.id; extraSearch.value = option.dataset.name; extraResults.classList.remove('is-open'); });
  document.addEventListener('click', function (event) { if (!event.target.closest('.ro-product-search')) extraResults.classList.remove('is-open'); });
}
</script>
<?php include 'includes/footer.php'; ?>
