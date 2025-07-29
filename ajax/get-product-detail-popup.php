<?php
require_once '../includes/functions.php';
header('Content-Type: text/html; charset=UTF-8');

session_start();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo '<div style="color:#c00;">Invalid product ID.</div>';
    exit;
}
$product = getProductById($id);
if (!$product) {
    echo '<div style="color:#c00;">Product not found.</div>';
    exit;
}
$productImages = getProductImages($product['id']);
$categoryPath = getCategoryPath($product['category_id']);
$discount = ($product['mrp'] > $product['selling_price']) ? ($product['mrp'] - $product['selling_price']) : 0;
$discountPercent = $product['mrp'] > 0 ? round((($product['mrp'] - $product['selling_price']) / $product['mrp']) * 100) : 0;
// Get current cart quantity for this product
$cartQty = 1;
if (isset($_SESSION['cart'][$product['id']])) {
    $cartQty = (int)$_SESSION['cart'][$product['id']];
} elseif (isLoggedIn()) {
    $cartItems = getCartItems($_SESSION['user_id']);
    foreach ($cartItems as $item) {
        if ($item['product_id'] == $product['id']) {
            $cartQty = (int)$item['quantity'];
            break;
        }
    }
}
// Check wishlist
$inWishlist = false;
if (isLoggedIn()) {
    $inWishlist = isInWishlist($_SESSION['user_id'], $product['id']);
} elseif (isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist'])) {
    $inWishlist = true;
}
// Helper for absolute image path
function abs_img($rel) {
    $rel = ltrim($rel, '/');
    return '/' . $rel;
}
?>
<div class="popup-product-card" style="max-width:520px;min-width:0;">
  <div style="display:flex;gap:28px;align-items:flex-start;flex-wrap:wrap;">
    <div style="flex:0 0 160px;max-width:160px;">
      <?php if ($discount > 0): ?>
        <div style="color:#23a036;font-weight:600;font-size:1.05em;margin-bottom:6px;">SAVE ₹<?php echo number_format($discount,0); ?><?php if ($discountPercent > 0): ?> (<?php echo $discountPercent; ?>% OFF)<?php endif; ?></div>
      <?php endif; ?>
      <img id="popupMainImage" src="<?php echo abs_img($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:160px;height:160px;object-fit:contain;border-radius:8px;box-shadow:0 2px 8px #eee;">
      <div style="display:flex;gap:6px;margin-top:10px;justify-content:center;flex-wrap:wrap;">
        <img class="popup-thumb" src="<?php echo abs_img($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:38px;height:38px;object-fit:cover;border-radius:5px;border:2px solid #007bff;cursor:pointer;" onclick="document.getElementById('popupMainImage').src=this.src;">
        <?php foreach ($productImages as $img): if ($img['image_path'] !== $product['main_image']): ?>
          <img class="popup-thumb" src="<?php echo abs_img($img['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:38px;height:38px;object-fit:cover;border-radius:5px;border:2px solid #eee;cursor:pointer;" onclick="document.getElementById('popupMainImage').src=this.src;">
        <?php endif; endforeach; ?>
      </div>
    </div>
    <div style="flex:1 1 220px;min-width:0;">
      <h2 style="margin:0 0 6px 0;font-size:1.35rem;font-weight:700;line-height:1.2;white-space:normal;"> <?php echo htmlspecialchars($product['name']); ?> </h2>
      <div style="font-size:1em;color:#222;margin-bottom:2px;">
        <?php if (!empty($product['sku'])): ?><span><b>SKU:</b> <?php echo htmlspecialchars($product['sku']); ?></span><br><?php endif; ?>
        <?php if (!empty($product['hsn'])): ?><span><b>HSN:</b> <?php echo htmlspecialchars($product['hsn']); ?></span><br><?php endif; ?>
      </div>
      <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px;flex-wrap:wrap;">
        <span style="background:#007bff;color:#fff;padding:6px 14px;border-radius:7px;font-weight:600;">MRP <s>₹<?php echo number_format($product['mrp'],0); ?></s></span>
        <span style="background:#23a036;color:#fff;padding:6px 14px;border-radius:7px;font-weight:600;">PAY ₹<?php echo number_format($product['selling_price'],0); ?></span>
        <button id="popupWishlistBtn" class="popup-wishlist-btn" style="background:#007bff;border:none;border-radius:7px;padding:6px 10px;display:inline-flex;align-items:center;cursor:pointer;outline:none;">
          <i class="fas fa-heart" style="color:<?php echo $inWishlist ? 'orange' : '#fff'; ?>;font-size:1.2em;"></i>
        </button>
      </div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
        <button type="button" class="btn-qty btn-qty-minus" style="width:36px;height:36px;font-size:1.3em;background:#f5f5f5;border:none;border-radius:4px;" aria-label="Decrease quantity" id="popupQtyMinus">-</button>
        <input type="number" id="popupQtyInput" value="<?php echo $cartQty; ?>" min="1" max="99" style="width:48px;text-align:center;font-size:1.1em;border:1px solid #ddd;border-radius:4px;height:36px;">
        <button type="button" class="btn-qty btn-qty-plus" style="width:36px;height:36px;font-size:1.3em;background:#f5f5f5;border:none;border-radius:4px;" aria-label="Increase quantity" id="popupQtyPlus">+</button>
        <button id="popupAddToCartBtn" class="btn btn-dark" style="margin-left:10px;padding:8px 24px;font-weight:600;letter-spacing:0.5px;">ADD TO CART</button>
      </div>
      <div style="font-size:1em;color:#444;margin-bottom:2px;">
        <b>Category:</b> 
        <?php foreach ($categoryPath as $i => $cat): ?>
          <a href="/category.php?slug=<?php echo $cat['slug']; ?>" style="color:#007bff;text-decoration:underline;"> <?php echo htmlspecialchars($cat['name']); ?> </a><?php if ($i < count($categoryPath) - 1) echo ' &raquo; '; ?>
        <?php endforeach; ?>
      </div>
      <div style="margin:10px 0 0 0;">
        <div style="font-size:1.07em;color:#222;font-weight:600;">PRODUCT DETAILS</div>
        <div style="font-size:1em;color:#444;max-height:70px;overflow:auto;white-space:pre-line;"> <?php echo nl2br(htmlspecialchars($product['description'])); ?> </div>
      </div>
      <?php if ($product['stock_quantity'] > 0): ?>
        <div style="color:#23a036;font-size:1em;margin-top:8px;"><b>STOCK:</b> <?php echo $product['stock_quantity']; ?> UNITS AVAILABLE</div>
      <?php else: ?>
        <div style="color:#c00;font-size:1em;margin-top:8px;"><b>OUT OF STOCK</b></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<!-- Removed inline <script> block for popup controls. All logic is now handled in popup.js via event delegation. --> 