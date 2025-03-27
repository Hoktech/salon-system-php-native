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
    
    // Get usage count (from invoices)
    $query = "
        SELECT COUNT(*) as usage_count, SUM(is.total) as revenue
        FROM invoice_services is
        JOIN invoices i ON is.invoice_id = i.id
        WHERE is.service_id = :service_id AND i.payment_status = 'paid'
    ";
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $query .= " AND i.branch_id = :branch_id";
    }
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':service_id', $id);
    
    if (!in_array($user['role'], ['admin'])) {
        $stmt->bindParam(':branch_id', $user['branch_id']);
    }
    
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get last usage
    $query = "
        SELECT i.invoice_date as last_usage
        FROM invoice_services is
        JOIN invoices i ON is.invoice_id = i.id
        WHERE is.service_id = :service_id
    ";
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $query .= " AND i.branch_id = :branch_id";
    }
    
    $query .= " ORDER BY i.invoice_date DESC LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':service_id', $id);
    
    if (!in_array($user['role'], ['admin'])) {
        $stmt->bindParam(':branch_id', $user['branch_id']);
    }
    
    $stmt->execute();
    $lastUsage = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get top employees
    $query = "
        SELECT u.id, u.full_name, COUNT(*) as service_count
        FROM invoice_services is
        JOIN invoices i ON is.invoice_id = i.id
        JOIN users u ON is.employee_id = u.id
        WHERE is.service_id = :service_id AND i.payment_status = 'paid'
    ";
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $query .= " AND i.branch_id = :branch_id";
    }
    
    $query .= " GROUP BY u.id ORDER BY service_count DESC LIMIT 5";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':service_id', $id);
    
    if (!in_array($user['role'], ['admin'])) {
        $stmt->bindParam(':branch_id', $user['branch_id']);
    }
    
    $stmt->execute();
    $topEmployees = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $topEmployees[] = $row;
    }
    
    // Prepare statistics data
    $statisticsData = [
        'usage_count' => (int)$stats['usage_count'],
        'revenue' => (float)$stats['revenue'],
        'last_usage' => $lastUsage ? $lastUsage['last_usage'] : null,
        'top_employees' => $topEmployees
    ];
    
    // Set response
    $response['status'] = "success";
    $response['message'] = "تم استرجاع إحصائيات الخدمة بنجاح";
    $response['data'] = $statisticsData;
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>