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
    
    // Get ID parameter
    if(!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("معرّف المستخدم مطلوب");
    }
    
    $id = intval($_GET['id']);
    
    // Build the SQL query
    $query = "
        SELECT u.id, u.username, u.full_name, u.email, u.phone, u.role, 
               u.branch_id, u.active, u.created_at, u.last_login,
               b.name as branch_name 
        FROM users u
        LEFT JOIN branches b ON u.branch_id = b.id
        WHERE u.id = :id
    ";
    
    // For non-admin users, restrict to their branch only
    $params = [':id' => $id];
    if ($user['role'] !== 'admin') {
        $query .= " AND u.branch_id = :branch_id";
        $params[':branch_id'] = $user['branch_id'];
    }
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Check if user exists
    if ($stmt->rowCount() === 0) {
        throw new Exception("المستخدم غير موجود أو ليس لديك صلاحية للوصول إليه");
    }
    
    // Fetch the user
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Remove password from response
    unset($userData['password']);
    
    // Get user permissions
    $permissionsQuery = "
        SELECT p.id, p.name, p.description
        FROM permissions p
        JOIN user_permissions up ON p.id = up.permission_id
        WHERE up.user_id = :user_id
    ";
    
    $stmt = $db->prepare($permissionsQuery);
    $stmt->bindParam(':user_id', $id);
    $stmt->execute();
    
    $permissions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $permissions[] = $row;
    }
    
    // Add permissions to user data
    $userData['permissions'] = $permissions;
    
    // Set response
    $response['status'] = "success";
    $response['message'] = "تم استرجاع بيانات المستخدم بنجاح";
    $response['data'] = $userData;
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>