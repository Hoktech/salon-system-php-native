<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and permission files
include_once '../config/database.php';
include_once '../config/helpers.php';
include_once '../auth/permissions.php';

// Initialize response array
$response = [
    "status" => true,
    "message" => "",
    "data" => null
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("طريقة طلب غير صالحة");
    }
    
    // Check user permissions
    $user = checkUserPermission(['admin', 'manager']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Prepare stats data
    $stats = [
        'total_products' => 0,
        'total_value' => 0,
        'low_stock_count' => 0,
        'out_of_stock_count' => 0,
        'categories' => []
    ];
    
    // Build query to get total products and value
    $query = "
        SELECT 
            COUNT(*) as total_products,
            SUM(stock_quantity * purchase_price) as total_value,
            SUM(CASE WHEN stock_quantity <= minimum_quantity AND stock_quantity > 0 THEN 1 ELSE 0 END) as low_stock_count,
            SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock_count
        FROM products
    ";
    
    // For non-admin users, restrict to their branch only
    $params = [];
    if ($user['role'] !== 'admin') {
        $query .= " WHERE branch_id = :branch_id";
        $params[':branch_id'] = $user['branch_id'];
    }
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Get stats
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stats['total_products'] = (int)$result['total_products'];
    $stats['total_value'] = (float)$result['total_value'];
    $stats['low_stock_count'] = (int)$result['low_stock_count'];
    $stats['out_of_stock_count'] = (int)$result['out_of_stock_count'];
    
    // Get inventory value by category
    $query = "
        SELECT 
            COALESCE(category, 'بدون فئة') as category,
            COUNT(*) as product_count,
            SUM(stock_quantity * purchase_price) as value
        FROM products
    ";
    
    // For non-admin users, restrict to their branch only
    if ($user['role'] !== 'admin') {
        $query .= " WHERE branch_id = :branch_id";
    } else {
        $query .= " WHERE 1=1";
    }
    
    $query .= " GROUP BY category ORDER BY value DESC";
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Get category stats
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stats['categories'][] = [
            'category' => $row['category'],
            'product_count' => (int)$row['product_count'],
            'value' => (float)$row['value']
        ];
    }
    
    // Set response
    $response['status'] = true;
    $response['message'] = "تم استرجاع إحصائيات المخزون بنجاح";
    $response['data'] = $stats;
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>