<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$statuses = getAllOrderStatuses();
echo "Total statuses found: " . count($statuses) . "\n";
foreach ($statuses as $status) {
    echo "ID: {$status['id']}, Name: {$status['name']}\n";
}
