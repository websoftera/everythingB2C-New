-- Allow one category to appear under multiple parent categories.
-- Safe to run repeatedly during deployment.

CREATE TABLE IF NOT EXISTS category_parent_assignments (
    category_id INT NOT NULL,
    parent_id INT NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (category_id, parent_id),
    INDEX idx_category_parent_parent (parent_id),
    INDEX idx_category_parent_primary (category_id, is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Preserve every existing category relationship as its primary relationship.
INSERT IGNORE INTO category_parent_assignments (category_id, parent_id, is_primary)
SELECT id, parent_id, 1
FROM categories
WHERE parent_id IS NOT NULL AND parent_id > 0;
