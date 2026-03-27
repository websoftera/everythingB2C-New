<?php
require_once 'config/database.php';

$sql = "SELECT os.name, o.order_status_id, COUNT(*) as count 
        FROM orders o 
        JOIN order_statuses os ON o.order_status_id = os.id 
        GROUP BY os.name, o.order_status_id 
        ORDER BY os.name, o.order_status_id";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Order counts per status ID:\n";
foreach ($results as $row) {
    echo "Status: {$row['name']}, ID: {$row['order_status_id']}, Count: {$row['count']}\n";
}

$sql2 = "SELECT id, name FROM order_statuses ORDER BY name, id";
$stmt = $pdo->query($sql2);
$all_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\nAll defined statuses:\n";
foreach ($all_statuses as $status) {
    echo "ID: {$status['id']}, Name: {$status['name']}\n";
}
