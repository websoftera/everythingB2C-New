<?php

/** Ensure the self-service seller application table exists. */
function ensureSellerApplicationsSchema(PDO $pdo): bool
{
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS seller_registration_applications (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            firm_type VARCHAR(40) NOT NULL,
            person_name VARCHAR(150) NOT NULL,
            mobile VARCHAR(20) NOT NULL,
            business_name VARCHAR(180) NOT NULL,
            email VARCHAR(190) NOT NULL,
            business_address TEXT NULL,
            business_category VARCHAR(150) NOT NULL,
            pan_number VARCHAR(20) NULL,
            gstin VARCHAR(20) NULL,
            gstin_document VARCHAR(255) NULL,
            pan_document VARCHAR(255) NULL,
            aadhaar_document VARCHAR(255) NULL,
            status ENUM('pending','contacted','approved','rejected') NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_seller_registration_status_created (status, created_at),
            INDEX idx_seller_registration_email (email),
            INDEX idx_seller_registration_mobile (mobile)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        return true;
    } catch (Throwable $e) {
        error_log('Seller application schema setup failed: ' . $e->getMessage());
        return false;
    }
}

/** Store a private application document and return its relative storage path. */
function storeSellerApplicationDocument(array $file, string $documentKey, string $storageDirectory): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('A document could not be uploaded. Please try again.');
    }
    if (($file['size'] ?? 0) < 1 || $file['size'] > 5 * 1024 * 1024 || !is_uploaded_file($file['tmp_name'] ?? '')) {
        throw new RuntimeException('Each document must be smaller than 5 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedTypes = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];
    if (!isset($allowedTypes[$mime])) {
        throw new RuntimeException('Upload documents as PDF, JPG, or PNG files.');
    }
    if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0750, true) && !is_dir($storageDirectory)) {
        throw new RuntimeException('Document storage is unavailable. Please contact us at 6355837347.');
    }

    $filename = $documentKey . '_' . bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
    $absolutePath = rtrim($storageDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
        throw new RuntimeException('A document could not be saved. Please try again.');
    }
    @chmod($absolutePath, 0640);
    return 'private_storage/seller_applications/' . $filename;
}

