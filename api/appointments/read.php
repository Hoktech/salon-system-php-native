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
        throw new Exception("Invalid request method");
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
    $date = isset($_GET['date']) ? sanitizeInput($_GET['date']) : null;
    $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
    $employee_id = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : null;
    $branch_id = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : null;
    $status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : null;
    
    // Build query conditions
    if ($date) {
        $where_clauses[] = "DATE(a.appointment_date) = :date";
        $params[':date'] = $date;
    }
    
    if ($customer_id) {
        $where_clauses[] = "a.customer_id = :customer_id";
        $params[':customer_id'] = $customer_id;
    }
    
    if ($employee_id) {
        $where_clauses[] = "a.employee_id = :employee_id";
        $params[':employee_id'] = $employee_id;
    }
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $where_clauses[] = "a.branch_id = :user_branch_id";
        $params[':user_branch_id'] = $user['branch_id'];
    } else if ($branch_id) {
        $where_clauses[] = "a.branch_id = :branch_id";
        $params[':branch_id'] = $branch_id;
    }
    
    if ($status) {
        $where_clauses[] = "a.status = :status";
        $params[':status'] = $status;
    }
    
    // Create WHERE clause
    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    // Build the SQL query with JOIN statements
    $query = "
        SELECT a.*, 
               c.name as customer_name, c.phone as customer_phone,
               e.name as employee_name,
               b.name as branch_name,
               s.name as service_name, s.duration as service_duration, s.price as service_price
        FROM appointments a
        LEFT JOIN customers c ON a.customer_id = c.id
        LEFT JOIN users e ON a.employee_id = e.id
        LEFT JOIN branches b ON a.branch_id = b.id
        LEFT JOIN services s ON a.service_id = s.id
        $where_sql
        ORDER BY a.appointment_date ASC
    ";
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Fetch records
    $appointments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
            "appointment_date" => $row['appointment_date'],
            "start_time" => $row['start_time'],
            "end_time" => $row['end_time'],
            "status" => $row['status'],
            "notes" => $row['notes'],
            "created_at" => $row['created_at'],
            "updated_at" => $row['updated_at']
        ];
        $appointments[] = $appointment;
    }
    
    // Set response
    $response['status'] = true;
    $response['message'] = "Appointments retrieved successfully";
    $response['data'] = $appointments;
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>