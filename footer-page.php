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

// Keep previously shared query-string links working, but show the new clean
// slug URL in the browser and search engines.
$requestedPath = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?? '');
if (basename($requestedPath) === 'footer-page.php') {
    header('Location: ' . footerPageUrl($page), true, 301);
    exit;
}

$pageTitle = (string)$page['title'];
include 'includes/header.php';
echo renderBreadcrumb(generateBreadcrumb($pageTitle));
renderFooterManagedPage($page);
include 'includes/footer.php';
