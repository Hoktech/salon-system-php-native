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
    
    // Initialize params
    $params = [];
    
    // Get current date
    $today = date('Y-m-d');
    
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
        WHERE a.date = :today AND a.status IN ('scheduled', 'confirmed')
    ";
    
    $params[':today'] = $today;
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $query .= " AND a.branch_id = :branch_id";
        $params[':branch_id'] = $user['branch_id'];
    }
    
    // If employee role, show only their appointments
    if ($user['role'] === 'employee') {
        $query .= " AND a.employee_id = :employee_id";
        $params[':employee_id'] = $user['id'];
    }
    
    // Order by time
    $query .= " ORDER BY a.start_time ASC";
    
    // Limit results if specified
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
    
    // Fetch appointments
    $appointments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $appointment = [
            "id" => $row['id'],
            "customer_name" => $row['customer_name'],
            "customer_phone" => $row['customer_phone'],
            "service_name" => $row['service_name'],
            "employee_name" => $row['employee_name'],
            "branch_name" => $row['branch_name'],
            "date" => $row['date'],
            "start_time" => $row['start_time'],
            "end_time" => $row['end_time'],
            "status" => $row['status'],
            "notes" => $row['notes']
        ];
        $appointments[] = $appointment;
    }
    
    // Set response
    $response['status'] = true;
    $response['message'] = "تم استرجاع المواعيد القادمة بنجاح";
    $response['data'] = $appointments;
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);