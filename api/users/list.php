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
    $user = checkUserPermission(['admin', 'manager']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize query parameters
    $params = [];
    $where_clauses = [];
    
    // Parse query parameters
    $role = isset($_GET['role']) ? sanitizeInput($_GET['role']) : null;
    $branch_id = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : null;
    $active = isset($_GET['active']) ? (int)$_GET['active'] : null;
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
    
    // Build query conditions
    if ($role) {
        $where_clauses[] = "u.role = :role";
        $params[':role'] = $role;
    }
    
    if ($active !== null) {
        $where_clauses[] = "u.active = :active";
        $params[':active'] = $active;
    }
    
    if ($search) {
        $where_clauses[] = "(u.username LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // For non-admin users, restrict to their branch only
    if ($user['role'] !== 'admin') {
        $where_clauses[] = "u.branch_id = :branch_id";
        $params[':branch_id'] = $user['branch_id'];
    } else if ($branch_id) {
        $where_clauses[] = "u.branch_id = :branch_id";
        $params[':branch_id'] = $branch_id;
    }
    
    // Create WHERE clause
    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    // Build the SQL query with JOIN to get branch name
    $query = "
        SELECT u.id, u.username, u.full_name, u.email, u.phone, u.role, 
               u.branch_id, u.active, u.created_at, u.last_login,
               b.name as branch_name 
        FROM users u
        LEFT JOIN branches b ON u.branch_id = b.id
        $where_sql
        ORDER BY u.full_name ASC
    ";
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Fetch all users
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Remove password from response
        unset($row['password']);
        $users[] = $row;
    }
    
    // Set response
    $response['status'] = "success";
    $response['message'] = "تم استرجاع قائمة المستخدمين بنجاح";
    $response['data'] = $users;
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
    $response['data'] = [];
}

// Return JSON response
echo json_encode($response);
?>