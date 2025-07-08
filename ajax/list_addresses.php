<?php
session_start();
require_once '../includes/functions.php';
if (!isLoggedIn()) exit;
$userId = $_SESSION['user_id'];
$userAddresses = getUserAddresses($userId);
if (!empty($userAddresses)) {
    echo '<ul class="account-address-list">';
    foreach ($userAddresses as $address) {
        echo '<li class="account-address-item ' . ($address['is_default'] ? 'account-badge-default' : '') . '" data-id="' . $address['id'] . '">';
        echo '<div class="account-address-info">';
        echo '<h6>' . htmlspecialchars($address['name']);
        if ($address['is_default']) echo ' <span class="account-badge account-badge-default">Default</span>';
        echo '</h6>';
        echo '<p>' . htmlspecialchars($address['phone']) . '</p>';
        echo '<p>' . htmlspecialchars($address['address_line1']) . '</p>';
        if ($address['address_line2']) echo '<p>' . htmlspecialchars($address['address_line2']) . '</p>';
        echo '<p>' . htmlspecialchars($address['city'] . ', ' . $address['state'] . ' - ' . $address['pincode']) . '</p>';
        echo '</div>';
        echo '<div>';
        if (!$address['is_default']) echo '<button class="account-btn account-btn-secondary" onclick="setDefaultAddress(' . $address['id'] . ')">Set as Default</button>';
        echo '<button class="account-btn" onclick="editAddress(' . $address['id'] . ')">Edit</button>';
        echo '<button class="account-btn account-btn-danger" onclick="deleteAddress(' . $address['id'] . ')">Delete</button>';
        echo '</div>';
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<div class="account-empty-state">';
    echo '<i class="fas fa-map-marker-alt"></i>';
    echo '<h5>No addresses saved</h5>';
    echo '<p>Add an address to make checkout easier</p>';
    echo '</div>';
} 