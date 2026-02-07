<?php
/**
 * DTDC Tracking Debug Script
 * Use this to test and debug DTDC tracking functionality
 */

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo "Please log in as admin first.";
    exit;
}

$testTrackingId = $_GET['tracking_id'] ?? 'DEMO_SITE00000001';
$action = $_GET['action'] ?? 'test';

?>
<!DOCTYPE html>
<html>
<head>
    <title>DTDC Tracking Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 20px; }
        .debug-section { margin: 20px 0; padding: 20px; background: #f5f5f5; border-radius: 8px; }
        .log-box { background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>DTDC Tracking Debug Tool</h1>
        
        <!-- Test Form -->
        <div class="debug-section">
            <h3>Test Tracking ID</h3>
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="tracking_id" class="form-control" value="<?php echo htmlspecialchars($testTrackingId); ?>" placeholder="Enter tracking ID">
                </div>
                <div class="col-md-6">
                    <button type="submit" name="action" value="test" class="btn btn-primary">Test Tracking</button>
                    <button type="submit" name="action" value="clear_cache" class="btn btn-warning">Clear Cache</button>
                </div>
            </form>
        </div>

        <?php if ($action === 'test'): ?>
            <!-- Test Results -->
            <div class="debug-section">
                <h3>Test Results for: <?php echo htmlspecialchars($testTrackingId); ?></h3>
                
                <?php
                // Start output buffering to capture error logs
                ob_start();
                
                // Test the tracking function
                $trackingData = getDTDCTracking($testTrackingId);
                
                $output = ob_get_clean();
                
                if ($trackingData) {
                    echo '<div class="alert alert-success"><strong>Success!</strong> Tracking data retrieved.</div>';
                    echo '<h5>Tracking Data:</h5>';
                    echo '<pre>' . htmlspecialchars(json_encode($trackingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
                    
                    // Check if it's mock data
                    if (isset($trackingData['is_mock_data']) && $trackingData['is_mock_data']) {
                        echo '<div class="alert alert-info"><strong>Note:</strong> This is demonstration/mock data. Live API is not accessible.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger"><strong>Error:</strong> Failed to retrieve tracking data.</div>';
                }
                
                // Display error log
                echo '<h5 class="mt-4">Debug Log:</h5>';
                echo '<div class="log-box">';
                if (!empty($output)) {
                    echo htmlspecialchars($output);
                } else {
                    echo '<span class="warning">No debug output captured. Check PHP error logs.</span>';
                }
                echo '</div>';
                
                // Check cache status
                echo '<h5 class="mt-4">Cache Status:</h5>';
                $cacheFile = __DIR__ . '/cache/dtdc_' . md5($testTrackingId) . '.json';
                if (file_exists($cacheFile)) {
                    $cacheStats = stat($cacheFile);
                    $cacheAge = time() - $cacheStats['mtime'];
                    echo '<p class="' . ($cacheAge < 300 ? 'success' : 'warning') . '">';
                    echo 'Cache file exists. Age: ' . $cacheAge . ' seconds (Max: 300)';
                    echo '</p>';
                } else {
                    echo '<p class="warning">No cache file found.</p>';
                }
                ?>
            </div>

            <!-- System Info -->
            <div class="debug-section">
                <h3>System Information</h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong>DTDC Service Enabled:</strong></td>
                            <td>
                                <?php
                                require_once 'includes/dtdc_api_new.php';
                                $api = new DTDCAPINew();
                                echo $api->isEnabled() ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Cache Directory:</strong></td>
                            <td>
                                <?php
                                $cacheDir = __DIR__ . '/cache';
                                if (is_dir($cacheDir) && is_writable($cacheDir)) {
                                    echo '<span class="success">✓ Writable</span> (' . $cacheDir . ')';
                                } else if (is_dir($cacheDir)) {
                                    echo '<span class="warning">⚠ Not writable</span> (' . $cacheDir . ')';
                                } else {
                                    echo '<span class="error">✗ Does not exist</span> (' . $cacheDir . ')';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>PHP Error Log:</strong></td>
                            <td>
                                <?php
                                $errorLog = ini_get('error_log');
                                if ($errorLog) {
                                    echo htmlspecialchars($errorLog);
                                    if (file_exists($errorLog) && is_readable($errorLog)) {
                                        echo ' <span class="success">(readable)</span>';
                                    }
                                } else {
                                    echo '<span class="warning">Using system default</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($action === 'clear_cache'): ?>
            <div class="debug-section">
                <h3>Cache Cleared</h3>
                <?php
                $cacheFile = __DIR__ . '/cache/dtdc_' . md5($testTrackingId) . '.json';
                if (file_exists($cacheFile)) {
                    if (unlink($cacheFile)) {
                        echo '<div class="alert alert-success">Cache cleared successfully.</div>';
                    } else {
                        echo '<div class="alert alert-danger">Failed to clear cache.</div>';
                    }
                } else {
                    echo '<div class="alert alert-info">No cache file found to clear.</div>';
                }
                ?>
            </div>
        <?php endif; ?>

        <hr>
        
        <!-- Instructions -->
        <div class="debug-section">
            <h3>How to Use This Debug Tool:</h3>
            <ol>
                <li>Enter a tracking ID (or use the default demo ID)</li>
                <li>Click "Test Tracking" to fetch tracking data</li>
                <li>Check the results and debug information below</li>
                <li>Use "Clear Cache" to clear cached data and fetch fresh data</li>
            </ol>
            
            <h4 class="mt-3">Expected Behavior:</h4>
            <ul>
                <li>If DTDC API is accessible, you'll see <strong>live tracking data</strong></li>
                <li>If DTDC API is not accessible, you'll see <strong>demonstration/mock data</strong> (marked as mock)</li>
                <li>Cache will store data for 5 minutes to reduce API calls</li>
            </ul>
            
            <h4 class="mt-3">Notes:</h4>
            <ul>
                <li>This tool requires admin login</li>
                <li>Check the debug log for detailed error messages</li>
                <li>Mock data is generated consistently for the same tracking ID</li>
            </ul>
        </div>
    </div>
</body>
</html>
