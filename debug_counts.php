<?php
require_once 'config/database.php';

$sql = "SELECT os.name, o.order_status_id, COUNT(*) as count 
        FROM orders o 
        JOIN order_statuses os ON o.order_status_id = os.id 
        GROUP BY os.name, o.order_status_id 
        ORDER BY os.name, o.order_status_id";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = "Order counts per status ID:\n";
foreach ($results as $row) {
    $output .= "Status: " . $row['name'] . ", ID: " . $row['order_status_id'] . ", Count: " . $row['count'] . "\n";
}

file_put_contents('debug_order_counts.txt', $output);
echo "Done. Check debug_order_counts.txt\n";
