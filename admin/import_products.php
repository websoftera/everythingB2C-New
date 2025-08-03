<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Import Products';
$success_message = '';
$error_message = '';
$import_results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['csv_file'];
        
        // Check file type
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file_extension !== 'csv') {
            $error_message = 'Please upload a valid CSV file.';
        } else {
            // Read CSV file
            $handle = fopen($file['tmp_name'], 'r');
            if ($handle) {
                // Skip BOM if present
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }
                
                // Read headers
                $headers = fgetcsv($handle);
                if (!$headers) {
                    $error_message = 'Invalid CSV file format.';
                } else {
                    // Validate required headers
                    $required_headers = ['Name', 'SKU', 'Description', 'MRP', 'Selling Price', 'Category Name'];
                    $missing_headers = array_diff($required_headers, $headers);
                    
                    if (!empty($missing_headers)) {
                        $error_message = 'Missing required columns: ' . implode(', ', $missing_headers);
                    } else {
                        // Process CSV data
                        $row_number = 1; // Start from 1 since we already read headers
                        $success_count = 0;
                        $error_count = 0;
                        
                        while (($data = fgetcsv($handle)) !== FALSE) {
                            $row_number++;
                            
                            try {
                                // Create associative array from headers and data
                                $row_data = array_combine($headers, $data);
                                
                                // Validate required fields
                                if (empty($row_data['Name']) || empty($row_data['SKU']) || 
                                    empty($row_data['Description']) || empty($row_data['MRP']) || 
                                    empty($row_data['Selling Price']) || empty($row_data['Category Name'])) {
                                    throw new Exception('Missing required fields');
                                }
                                
                                // Validate numeric fields
                                if (!is_numeric($row_data['MRP']) || !is_numeric($row_data['Selling Price'])) {
                                    throw new Exception('MRP and Selling Price must be numeric');
                                }
                                
                                $mrp = floatval($row_data['MRP']);
                                $selling_price = floatval($row_data['Selling Price']);
                                
                                if ($mrp <= 0 || $selling_price <= 0) {
                                    throw new Exception('MRP and Selling Price must be greater than 0');
                                }
                                
                                if ($selling_price > $mrp) {
                                    throw new Exception('Selling Price cannot be greater than MRP');
                                }
                                
                                // Check if SKU already exists
                                $stmt = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
                                $stmt->execute([$row_data['SKU']]);
                                if ($stmt->fetch()) {
                                    throw new Exception('SKU already exists');
                                }
                                
                                // Get or create category
                                $category_name = trim($row_data['Category Name']);
                                $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
                                $stmt->execute([$category_name]);
                                $category = $stmt->fetch();
                                
                                if (!$category) {
                                    // Create new category
                                    $category_slug = createSlug($category_name);
                                    $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
                                    $stmt->execute([$category_name, $category_slug]);
                                    $category_id = $pdo->lastInsertId();
                                } else {
                                    $category_id = $category['id'];
                                }
                                
                                // Prepare product data
                                $product_data = [
                                    'name' => trim($row_data['Name']),
                                    'slug' => createSlug($row_data['Name']),
                                    'sku' => trim($row_data['SKU']),
                                    'hsn' => isset($row_data['HSN']) ? trim($row_data['HSN']) : null,
                                    'description' => trim($row_data['Description']),
                                    'mrp' => $mrp,
                                    'selling_price' => $selling_price,
                                    'discount_percentage' => calculateDiscountPercentage($mrp, $selling_price),
                                    'category_id' => $category_id,
                                    'stock_quantity' => isset($row_data['Stock Quantity']) ? intval($row_data['Stock Quantity']) : 0,
                                    'max_quantity_per_order' => isset($row_data['Max Quantity Per Order']) ? intval($row_data['Max Quantity Per Order']) : null,
                                    'gst_type' => 'sgst_cgst',
                                    'gst_rate' => isset($row_data['GST Rate']) ? floatval($row_data['GST Rate']) : 18,
                                    'is_active' => isset($row_data['Is Active']) ? (strtolower($row_data['Is Active']) === 'yes' ? 1 : 0) : 1,
                                    'is_featured' => isset($row_data['Is Featured']) ? (strtolower($row_data['Is Featured']) === 'yes' ? 1 : 0) : 0,
                                    'is_discounted' => isset($row_data['Is Discounted']) ? (strtolower($row_data['Is Discounted']) === 'yes' ? 1 : 0) : 0,
                                    'main_image' => isset($row_data['Main Image']) ? trim($row_data['Main Image']) : null
                                ];
                                
                                // Insert product
                                $stmt = $pdo->prepare("INSERT INTO products (name, slug, sku, hsn, description, mrp, selling_price, discount_percentage, category_id, stock_quantity, max_quantity_per_order, gst_type, gst_rate, is_active, is_featured, is_discounted, main_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $stmt->execute([
                                    $product_data['name'],
                                    $product_data['slug'],
                                    $product_data['sku'],
                                    $product_data['hsn'],
                                    $product_data['description'],
                                    $product_data['mrp'],
                                    $product_data['selling_price'],
                                    $product_data['discount_percentage'],
                                    $product_data['category_id'],
                                    $product_data['stock_quantity'],
                                    $product_data['max_quantity_per_order'],
                                    $product_data['gst_type'],
                                    $product_data['gst_rate'],
                                    $product_data['is_active'],
                                    $product_data['is_featured'],
                                    $product_data['is_discounted'],
                                    $product_data['main_image']
                                ]);
                                
                                $success_count++;
                                $import_results[] = [
                                    'row' => $row_number,
                                    'status' => 'success',
                                    'message' => 'Product "' . $product_data['name'] . '" imported successfully'
                                ];
                                
                            } catch (Exception $e) {
                                $error_count++;
                                $import_results[] = [
                                    'row' => $row_number,
                                    'status' => 'error',
                                    'message' => $e->getMessage()
                                ];
                            }
                        }
                        
                        fclose($handle);
                        
                        if ($success_count > 0) {
                            $success_message = "Import completed! $success_count products imported successfully.";
                            if ($error_count > 0) {
                                $success_message .= " $error_count rows had errors.";
                            }
                        } else {
                            $error_message = "No products were imported. Please check the CSV format.";
                        }
                    }
                }
            } else {
                $error_message = 'Could not read the uploaded file.';
            }
        }
    } else {
        $error_message = 'Please select a CSV file to upload.';
    }
}

// Helper function to create slug
function createSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="everythingb2c-admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="everythingb2c-main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Import Products Content -->
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">Import Products</h1>
                                <a href="products.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Products
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Import Products from CSV</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="csv_file" class="form-label">Select CSV File</label>
                                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                                            <div class="form-text">Upload a CSV file with product data</div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload"></i> Import Products
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">CSV Format Guide</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Required Columns:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Name</strong> - Product name</li>
                                        <li><strong>SKU</strong> - Unique product code</li>
                                        <li><strong>Description</strong> - Product description</li>
                                        <li><strong>MRP</strong> - Maximum retail price</li>
                                        <li><strong>Selling Price</strong> - Actual selling price</li>
                                        <li><strong>Category Name</strong> - Product category</li>
                                    </ul>
                                    
                                    <h6>Optional Columns:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>HSN</strong> - HSN code</li>
                                        <li><strong>Stock Quantity</strong> - Available stock</li>
                                        <li><strong>Max Quantity Per Order</strong> - Order limit</li>
                                        <li><strong>GST Rate</strong> - GST percentage (0, 5, 12, 18)</li>
                                        <li><strong>Is Active</strong> - Yes/No</li>
                                        <li><strong>Is Featured</strong> - Yes/No</li>
                                        <li><strong>Is Discounted</strong> - Yes/No</li>
                                        <li><strong>Main Image</strong> - Image path</li>
                                    </ul>
                                    
                                    <div class="mt-3">
                                        <a href="sample_products.csv" class="btn btn-success btn-sm" download>
                                            <i class="fas fa-download"></i> Download Sample CSV
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($import_results)): ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Import Results</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Row</th>
                                                <th>Status</th>
                                                <th>Message</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($import_results as $result): ?>
                                                <tr class="<?php echo $result['status'] === 'success' ? 'table-success' : 'table-danger'; ?>">
                                                    <td><?php echo $result['row']; ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $result['status'] === 'success' ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo ucfirst($result['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($result['message']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html> 