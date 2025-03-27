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
    
    // Check specific permission if it's defined in the user's session
    if (isset($_SESSION['permissions']) && !empty($_SESSION['permissions'])) {
        if ($_SESSION['role'] !== 'admin' && !in_array('customers_view', $_SESSION['permissions'])) {
            throw new Exception("ليس لديك صلاحية لعرض بيانات العملاء");
        }
    }
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize query parameters
    $params = [];
    $where_clauses = [];
    
    // Parse query parameters
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
    $branch_id = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : null;
    $gender = isset($_GET['gender']) ? sanitizeInput($_GET['gender']) : null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    // Build query conditions
    if ($id) {
        $where_clauses[] = "c.id = :id";
        $params[':id'] = $id;
    }
    
    if ($search) {
        $where_clauses[] = "(c.full_name LIKE :search OR c.phone LIKE :search OR c.email LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        if ($user['branch_id']) {
            $where_clauses[] = "c.branch_id = :user_branch_id";
            $params[':user_branch_id'] = $user['branch_id'];
        }
    } else if ($branch_id) {
        $where_clauses[] = "c.branch_id = :branch_id";
        $params[':branch_id'] = $branch_id;
    }
    
    if ($gender && in_array($gender, ['male', 'female'])) {
        $where_clauses[] = "c.gender = :gender";
        $params[':gender'] = $gender;
    }
    
    // Create WHERE clause
    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    // Calculate total count for pagination
    $count_query = "SELECT COUNT(*) as total FROM customers c $where_sql";
    $count_stmt = $db->prepare($count_query);
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Build the SQL query with JOIN statements and LIMIT
    $query = "
        SELECT c.*, b.name as branch_name
        FROM customers c
        LEFT JOIN branches b ON c.branch_id = b.id
        $where_sql
        ORDER BY c.full_name ASC
        LIMIT :limit OFFSET :offset
    ";
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch records
    $customers = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $customer = [
            "id" => $row['id'],
            "full_name" => $row['full_name'],
            "phone" => $row['phone'],
            "email" => $row['email'],
            "birthdate" => $row['birthdate'],
            "gender" => $row['gender'],
            "address" => $row['address'],
            "notes" => $row['notes'],
            "loyalty_points" => $row['loyalty_points'],
            "branch_id" => $row['branch_id'],
            "branch_name" => $row['branch_name'],
            "created_at" => $row['created_at']
        ];
        
        // If this is a request for a single customer, get their appointments and invoices
        if ($id && $id == $row['id']) {
            // Get recent appointments
            $appt_query = "
                SELECT a.id, a.date, a.start_time, a.status, s.name as service_name
                FROM appointments a
                LEFT JOIN services s ON a.service_id = s.id
                WHERE a.customer_id = :customer_id
                ORDER BY a.date DESC, a.start_time DESC
                LIMIT 5
            ";
            $appt_stmt = $db->prepare($appt_query);
            $appt_stmt->bindParam(":customer_id", $row['id']);
            $appt_stmt->execute();
            $customer['appointments'] = $appt_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get recent invoices
            $inv_query = "
                SELECT i.id, i.invoice_number, i.final_amount, i.payment_method, i.payment_status, i.invoice_date
                FROM invoices i
                WHERE i.customer_id = :customer_id
                ORDER BY i.invoice_date DESC
                LIMIT 5
            ";
            $inv_stmt = $db->prepare($inv_query);
            $inv_stmt->bindParam(":customer_id", $row['id']);
            $inv_stmt->execute();
            $customer['invoices'] = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $customers[] = $customer;
    }
    
    // Set response
    $response['status'] = true;
    $response['message'] = count($customers) > 0 ? "تم استرجاع بيانات العملاء بنجاح" : "لا توجد بيانات للعرض";
    $response['data'] = $customers;
    $response['metadata'] = [
        'total' => (int)$total_rows,
        'limit' => $limit,
        'offset' => $offset,
        'count' => count($customers)
    ];
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>