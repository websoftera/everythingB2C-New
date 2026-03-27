<?php
require_once 'config/database.php';

try {
    $pdo->beginTransaction();

    // 1. Get all statuses grouped by name
    $stmt = $pdo->query("SELECT name, MIN(id) as canonical_id, GROUP_CONCAT(id) as all_ids 
                         FROM order_statuses 
                         GROUP BY name 
                         HAVING COUNT(*) > 1");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Consolidating duplicates...\n";

    foreach ($duplicates as $row) {
        $name = $row['name'];
        $canonicalId = $row['canonical_id'];
        $allIds = explode(',', $row['all_ids']);
        
        // Filter out the canonical ID from the list of IDs to be replaced
        $otherIds = array_filter($allIds, function($id) use ($canonicalId) {
            return $id != $canonicalId;
        });

        if (empty($otherIds)) continue;

        $otherIdsStr = implode(',', $otherIds);
        echo "Processing '{$name}': Mapping IDs ({$otherIdsStr}) -> {$canonicalId}\n";

        // 2. Update orders table
        $updateOrders = $pdo->prepare("UPDATE orders SET order_status_id = ? WHERE order_status_id IN ($otherIdsStr)");
        $updateOrders->execute([$canonicalId]);
        echo " - Updated " . $updateOrders->rowCount() . " orders.\n";

        // 3. Update order_status_history table
        // Check if table exists first
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'order_status_history'");
        if ($tableCheck->rowCount() > 0) {
            $updateHistory = $pdo->prepare("UPDATE order_status_history SET order_status_id = ? WHERE order_status_id IN ($otherIdsStr)");
            $updateHistory->execute([$canonicalId]);
            echo " - Updated " . $updateHistory->rowCount() . " history records.\n";
        }

        // 4. Delete redundant statuses
        $deleteStatuses = $pdo->query("DELETE FROM order_statuses WHERE id IN ($otherIdsStr)");
        echo " - Deleted " . $deleteStatuses->rowCount() . " redundant status records.\n";
    }

    $pdo->commit();
    echo "\nConsolidation complete!\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
}
