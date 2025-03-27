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
    "status" => "success",
    "message" => "",
    "data" => []
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("طريقة طلب غير صالحة");
    }
    
    // Check user permissions
    $user = checkUserPermission(['admin', 'manager', 'cashier', 'employee']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Build the SQL query
    $query = "
        SELECT DISTINCT category
        FROM services
        WHERE category IS NOT NULL AND category != ''
    ";
    
    // For non-admin users, restrict to their branch only
    $params = [];
    if (!in_array($user['role'], ['admin'])) {
        $query .= " AND (branch_id = :branch_id OR branch_id IS NULL)";
        $params[':branch_id'] = $user['branch_id'];
    }
    
    // Add order by
    $query .= " ORDER BY category ASC";
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Fetch all categories
    $categories = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row['category'];
    }
    
    // Set response
    $response['status'] = "success";
    $response['message'] = "تم استرجاع قائمة الفئات بنجاح";
    $response['data'] = $categories;
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
    $response['data'] = [];
}

// Return JSON response
echo json_encode($response);
?>