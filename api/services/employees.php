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
    
    // Get ID parameter
    if(!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("معرّف الخدمة مطلوب");
    }
    
    $id = intval($_GET['id']);
    
    // Check if service exists
    $query = "SELECT id, branch_id FROM services WHERE id = :id";
    $params = [':id' => $id];
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $query .= " AND (branch_id = :branch_id OR branch_id IS NULL)";
        $params[':branch_id'] = $user['branch_id'];
    }
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("الخدمة غير موجودة أو ليس لديك صلاحية للوصول إليها");
    }
    
    $serviceData = $stmt->fetch(PDO::FETCH_ASSOC);
    $branch_id = $serviceData['branch_id'];
    
    // Get employees who can perform the service
    $query = "
        SELECT u.id, u.full_name, u.role, 'متخصص' as role_name
        FROM users u
        JOIN employee_services es ON u.id = es.employee_id
        WHERE es.service_id = :service_id AND u.active = 1 AND u.role = 'employee'
    ";
    
    // If service is branch-specific, get employees from that branch
    if ($branch_id) {
        $query .= " AND u.branch_id = :branch_id";
    }
    
    $query .= " ORDER BY u.full_name ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':service_id', $id);
    
    if ($branch_id) {
        $stmt->bindParam(':branch_id', $branch_id);
    }
    
    $stmt->execute();
    
    // Fetch all employees
    $employees = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $employees[] = $row;
    }
    
    // Set response
    $response['status'] = "success";
    $response['message'] = "تم استرجاع قائمة الموظفين بنجاح";
    $response['data'] = $employees;
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
    $response['data'] = [];
}

// Return JSON response
echo json_encode($response);
?>