<?php
session_start();

// Clear seller session
unset($_SESSION['seller_id']);
unset($_SESSION['seller_user_id']);
unset($_SESSION['seller_name']);
unset($_SESSION['seller_email']);
unset($_SESSION['seller_business_name']);

// Destroy session
session_destroy();

// Redirect to seller login
header('Location: login.php');
exit;
?>
