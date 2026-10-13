<?php
// Deployment command: php database/migrate_repeat_orders.php
require_once __DIR__ . '/../config/database.php';
function addRepeatColumn(PDO $pdo, string $table, string $column, string $definition): void {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?"); $stmt->execute([$column]);
    if (!$stmt->fetch()) $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
}
addRepeatColumn($pdo, 'orders', 'reordered_from_order_id', 'INT NULL');
addRepeatColumn($pdo, 'order_items', 'variation_id', 'INT NULL');
addRepeatColumn($pdo, 'order_items', 'selected_attributes', 'TEXT NULL');
try { $pdo->exec('CREATE INDEX idx_orders_reordered_from ON orders (reordered_from_order_id)'); } catch (PDOException $e) { }
echo "Repeat-order migration completed.\n";
