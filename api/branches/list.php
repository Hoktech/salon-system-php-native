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
    $active = isset($_GET['active']) ? (int)$_GET['active'] : null;
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
    
    // Build query conditions
    if ($active !== null) {
        $where_clauses[] = "b.active = :active";
        $params[':active'] = $active;
    }
    
    if ($search) {
        $where_clauses[] = "(b.name LIKE :search OR b.address LIKE :search OR b.phone LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // For non-admin users, restrict to their branch only
    if ($user['role'] !== 'admin') {
        $where_clauses[] = "b.id = :branch_id";
        $params[':branch_id'] = $user['branch_id'];
    }
    
    // Create WHERE clause
    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    // Build the SQL query with JOIN to get manager name
    $query = "
        SELECT b.*, u.full_name as manager_name
        FROM branches b
        LEFT JOIN users u ON b.manager_id = u.id
        $where_sql
        ORDER BY b.name ASC
    ";
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Fetch all branches
    $branches = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $branches[] = $row;
    }
    
    // Set response
    $response['status'] = "success";
    $response['message'] = "تم استرجاع قائمة الفروع بنجاح";
    $response['data'] = $branches;
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
    $response['data'] = [];
}

// Return JSON response
echo json_encode($response);