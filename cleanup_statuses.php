<?php
/**
 * Order Status Cleanup Script
 * 
 * This script identifies duplicate order statuses in the database
 * and provides SQL to merge them if necessary.
 */

require_once 'config/database.php';
require_once 'includes/functions.php';

echo "Order Status Cleanup Diagnostic\n";
echo "===============================\n\n";

try {
    // 1. Find duplicate status names
    $stmt = $pdo->query("SELECT name, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id ASC) as ids 
                         FROM order_statuses 
                         GROUP BY name 
                         HAVING count > 1");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($duplicates)) {
        echo "No duplicate status names found in the database. Excellent!\n";
        exit;
    }

    echo "Found " . count($duplicates) . " duplicate status clusters:\n\n";

    foreach ($duplicates as $row) {
        $name = $row['name'];
        $count = $row['count'];
        $ids = explode(',', $row['ids']);
        $keepId = $ids[0];
        $mergeIds = array_slice($ids, 1);
        
        echo "Status Name: '$name' ($count occurrences)\n";
        echo "  IDs found: " . implode(', ', $ids) . "\n";
        echo "  Recommended action: Keep ID $keepId, merge IDs " . implode(', ', $mergeIds) . " into it.\n\n";
        
        echo "  SQL to merge orders:\n";
        echo "  UPDATE orders SET order_status_id = $keepId WHERE order_status_id IN (" . implode(',', $mergeIds) . ");\n";
        echo "  UPDATE order_status_history SET order_status_id = $keepId WHERE order_status_id IN (" . implode(',', $mergeIds) . ");\n";
        echo "  DELETE FROM order_statuses WHERE id IN (" . implode(',', $mergeIds) . ");\n";
        echo "  " . str_repeat('-', 40) . "\n\n";
    }

    echo "\nIMPORTANT: Back up your database before running any of the SQL above.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
