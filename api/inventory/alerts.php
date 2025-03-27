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
    "data" => []
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("طريقة طلب غير صالحة");
    }
    
    // Check user permissions
    $user = checkUserPermission(['admin', 'manager', 'cashier']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Build query to get products with low stock
    $query = "
        SELECT p.id, p.name as product_name, p.category, p.stock_quantity, p.minimum_quantity, 
               p.for_sale, p.for_internal_use, p.branch_id
        FROM products p
        WHERE p.stock_quantity <= p.minimum_quantity
    ";
    
    // For non-admin users, restrict to their branch only
    $params = [];
    if ($user['role'] !== 'admin') {
        $query .= " AND p.branch_id = :branch_id";
        $params[':branch_id'] = $user['branch_id'];
    }
    
    // Add limits if specified
    if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
        $limit = (int)$_GET['limit'];
        $query .= " LIMIT :limit";
        $params[':limit'] = $limit;
    }
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Fetch all alerts
    $alerts = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $alerts[] = $row;
    }
    
    // Set response
    $response['status'] = true;
    $response['message'] = "تم استرجاع تنبيهات المخزون بنجاح";
    $response['data'] = $alerts;
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
    $response['data'] = [];
}

// Return JSON response
echo json_encode($response);
?>