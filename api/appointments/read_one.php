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
    "status" => false,
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
        throw new Exception("معرّف الموعد مطلوب");
    }
    
    $id = intval($_GET['id']);
    
    // Build the SQL query with JOIN statements
    $query = "
        SELECT a.*, 
               c.full_name as customer_name, c.phone as customer_phone,
               u.full_name as employee_name,
               b.name as branch_name,
               s.name as service_name, s.duration as service_duration, s.price as service_price
        FROM appointments a
        LEFT JOIN customers c ON a.customer_id = c.id
        LEFT JOIN users u ON a.employee_id = u.id
        LEFT JOIN branches b ON a.branch_id = b.id
        LEFT JOIN services s ON a.service_id = s.id
        WHERE a.id = :id
    ";
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $query .= " AND a.branch_id = :branch_id";
    }
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if (!in_array($user['role'], ['admin'])) {
        $stmt->bindParam(':branch_id', $user['branch_id'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    // Check if appointment exists
    if ($stmt->rowCount() === 0) {
        throw new Exception("الموعد غير موجود أو ليس لديك صلاحية للوصول إليه");
    }
    
    // Fetch the appointment
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Format appointment data for response
    $appointment = [
        "id" => $row['id'],
        "customer" => [
            "id" => $row['customer_id'],
            "name" => $row['customer_name'],
            "phone" => $row['customer_phone']
        ],
        "employee" => [
            "id" => $row['employee_id'],
            "name" => $row['employee_name']
        ],
        "branch" => [
            "id" => $row['branch_id'],
            "name" => $row['branch_name']
        ],
        "service" => [
            "id" => $row['service_id'],
            "name" => $row['service_name'],
            "duration" => $row['service_duration'],
            "price" => $row['service_price']
        ],
        "date" => $row['date'],
        "start_time" => $row['start_time'],
        "end_time" => $row['end_time'],
        "status" => $row['status'],
        "notes" => $row['notes'],
        "created_at" => $row['created_at']
    ];
    
    // Set response
    $response['status'] = true;
    $response['message'] = "تم استرجاع بيانات الموعد بنجاح";
    $response['data'] = $appointment;
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>