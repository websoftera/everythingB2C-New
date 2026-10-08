<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/footer_pages.php';

$slug = footerPageSlug((string)($_GET['slug'] ?? ''));
$page = $slug === '' ? null : getFooterPageBySlug($pdo, $slug);
if (!$page || trim((string)$page['content']) === '') {
    http_response_code(404);
    exit('Page not found.');
}

$pageTitle = (string)$page['title'];
include 'includes/header.php';
echo renderBreadcrumb(generateBreadcrumb($pageTitle));
renderFooterManagedPage($page);
include 'includes/footer.php';
