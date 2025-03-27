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
    
    // Initialize query parameters
    $params = [];
    $where_clauses = [];
    
    // Parse query parameters
    $category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
    $active = isset($_GET['active']) ? (int)$_GET['active'] : null;
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
    
    // Build query conditions
    if ($category) {
        $where_clauses[] = "s.category = :category";
        $params[':category'] = $category;
    }
    
    if ($active !== null) {
        $where_clauses[] = "s.active = :active";
        $params[':active'] = $active;
    }
    
    if ($search) {
        $where_clauses[] = "(s.name LIKE :search OR s.description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $where_clauses[] = "(s.branch_id = :branch_id OR s.branch_id IS NULL)";
        $params[':branch_id'] = $user['branch_id'];
    } else if (isset($_GET['branch_id']) && !empty($_GET['branch_id'])) {
        $branch_id = (int)$_GET['branch_id'];
        $where_clauses[] = "(s.branch_id = :branch_id OR s.branch_id IS NULL)";
        $params[':branch_id'] = $branch_id;
    }
    
    // Create WHERE clause
    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    // Build the SQL query with LEFT JOIN to get branch name
    $query = "
        SELECT s.*, b.name AS branch_name
        FROM services s
        LEFT JOIN branches b ON s.branch_id = b.id
        $where_sql
        ORDER BY s.name ASC
    ";
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Fetch all services
    $services = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $services[] = $row;
    }
    
    // Set response
    $response['status'] = "success";
    $response['message'] = "تم استرجاع قائمة الخدمات بنجاح";
    $response['data'] = $services;
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
    $response['data'] = [];
}

// Return JSON response
echo json_encode($response);
?>