<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$statuses = getAllOrderStatuses();
echo "Total Statuses found: " . count($statuses) . "\n";
foreach ($statuses as $status) {
    echo "ID: " . $status['id'] . " | Name: " . $status['name'] . " | Sort: " . $status['sort_order'] . "\n";
}
?>
