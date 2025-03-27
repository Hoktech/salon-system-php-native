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
    $user = checkUserPermission(['admin', 'manager', 'cashier', 'employee']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get ID parameter
    if(!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("معرّف الخدمة مطلوب");
    }
    
    $id = intval($_GET['id']);
    
    // Build the SQL query
    $query = "
        SELECT s.*, b.name AS branch_name
        FROM services s
        LEFT JOIN branches b ON s.branch_id = b.id
        WHERE s.id = :id
    ";
    
    // For non-admin users, restrict to their branch only
    $params = [':id' => $id];
    if (!in_array($user['role'], ['admin'])) {
        $query .= " AND (s.branch_id = :branch_id OR s.branch_id IS NULL)";
        $params[':branch_id'] = $user['branch_id'];
    }
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Check if service exists
    if ($stmt->rowCount() === 0) {
        throw new Exception("الخدمة غير موجودة أو ليس لديك صلاحية للوصول إليها");
    }
    
    // Fetch the service
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Set response
    $response['status'] = "success";
    $response['message'] = "تم استرجاع بيانات الخدمة بنجاح";
    $response['data'] = $service;
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>