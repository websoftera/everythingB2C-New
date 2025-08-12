<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Check if a pincode is serviceable
 */
function isPincodeServiceable($pincode) {
    global $pdo;
    
    $pincode = trim($pincode);
    if (empty($pincode) || strlen($pincode) !== 6) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT id FROM serviceable_pincodes WHERE pincode = ? AND is_active = 1");
    $stmt->execute([$pincode]);
    
    return $stmt->rowCount() > 0;
}

/**
 * Get popup settings
 */
function getPopupSettings() {
    global $pdo;
    
    $settings = [];
    $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM popup_settings");
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

/**
 * Update popup settings
 */
function updatePopupSettings($settings) {
    global $pdo;
    
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("UPDATE popup_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$value, $key]);
    }
    
    return true;
}

/**
 * Add serviceable pincodes (comma-separated)
 */
function addServiceablePincodes($pincodesString) {
    global $pdo;
    
    $pincodes = array_map('trim', explode(',', $pincodesString));
    $added = 0;
    $errors = [];
    
    foreach ($pincodes as $pincode) {
        if (empty($pincode)) continue;
        
        // Validate pincode format (6 digits)
        if (!preg_match('/^\d{6}$/', $pincode)) {
            $errors[] = "Invalid pincode format: $pincode";
            continue;
        }
        
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO serviceable_pincodes (pincode) VALUES (?)");
            if ($stmt->execute([$pincode])) {
                $added++;
            }
        } catch (Exception $e) {
            $errors[] = "Error adding pincode $pincode: " . $e->getMessage();
        }
    }
    
    return ['added' => $added, 'errors' => $errors];
}

/**
 * Get all serviceable pincodes
 */
function getAllServiceablePincodes() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, pincode, city, state, is_active, created_at FROM serviceable_pincodes ORDER BY pincode");
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Delete a serviceable pincode
 */
function deleteServiceablePincode($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM serviceable_pincodes WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Toggle pincode active status
 */
function togglePincodeStatus($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE serviceable_pincodes SET is_active = NOT is_active WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Check if popup should be shown (based on session)
 */
function shouldShowDeliveryPopup() {
    if (!isset($_SESSION['delivery_popup_shown'])) {
        return true;
    }
    return false;
}

/**
 * Mark popup as shown in session
 */
function markDeliveryPopupAsShown() {
    $_SESSION['delivery_popup_shown'] = true;
}
?>
