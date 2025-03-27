<?php
// api/customers/read_one.php
// This file handles retrieving a specific customer's details

// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and helper files
include_once '../config/database.php';
include_once '../config/helpers.php';
include_once '../auth/permissions.php';

// Initialize response array for API consistency
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
    
    // Check user authentication and permissions
    $user = checkUserPermission(['admin', 'manager', 'cashier', 'employee']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Validate and sanitize customer ID
    if (!isset($_GET['id'])) {
        throw new Exception("معرف العميل مطلوب");
    }
    
    $customer_id = (int)$_GET['id'];
    if ($customer_id <= 0) {
        throw new Exception("معرف العميل غير صالح");
    }
    
    // Get customer details
    $query = "
        SELECT c.*, b.name as branch_name
        FROM customers c
        LEFT JOIN branches b ON c.branch_id = b.id
        WHERE c.id = :id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $customer_id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("العميل غير موجود");
    }
    
    // Get customer data
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // For non-admin users, check if they can access this customer's data based on branch
    if ($user['role'] !== 'admin' && $user['branch_id'] && $customer['branch_id'] != $user['branch_id']) {
        throw new Exception("ليس لديك صلاحية للوصول إلى بيانات هذا العميل");
    }
    
    // Get customer's recent visits (invoices)
    $visits_query = "
        SELECT 
            i.id,
            DATE(i.invoice_date) as date,
            GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as services,
            i.final_amount as amount,
            GROUP_CONCAT(DISTINCT u.full_name SEPARATOR ', ') as employee,
            i.notes
        FROM 
            invoices i
        LEFT JOIN 
            invoice_services is2 ON i.id = is2.invoice_id
        LEFT JOIN 
            services s ON is2.service_id = s.id
        LEFT JOIN 
            users u ON is2.employee_id = u.id
        WHERE 
            i.customer_id = :customer_id
        GROUP BY 
            i.id
        ORDER BY 
            i.invoice_date DESC
        LIMIT 10
    ";
    
    $visits_stmt = $db->prepare($visits_query);
    $visits_stmt->bindParam(":customer_id", $customer_id);
    $visits_stmt->execute();
    $visits = $visits_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get favorite services
    $favorite_query = "
        SELECT 
            s.id, 
            s.name, 
            COUNT(*) as count
        FROM 
            invoice_services is2
        JOIN 
            invoices i ON is2.invoice_id = i.id
        JOIN 
            services s ON is2.service_id = s.id
        WHERE 
            i.customer_id = :customer_id
        GROUP BY 
            s.id, s.name
        ORDER BY 
            count DESC
        LIMIT 5
    ";
    
    $favorite_stmt = $db->prepare($favorite_query);
    $favorite_stmt->bindParam(":customer_id", $customer_id);
    $favorite_stmt->execute();
    $favorite_services = $favorite_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total and average spent
    $spent_query = "
        SELECT 
            COUNT(id) as visit_count,
            COALESCE(SUM(final_amount), 0) as total_amount
        FROM 
            invoices
        WHERE 
            customer_id = :customer_id AND 
            payment_status = 'paid'
    ";
    
    $spent_stmt = $db->prepare($spent_query);
    $spent_stmt->bindParam(":customer_id", $customer_id);
    $spent_stmt->execute();
    $spent_data = $spent_stmt->fetch(PDO::FETCH_ASSOC);
    
    $total_spent = floatval($spent_data['total_amount']);
    $visit_count = intval($spent_data['visit_count']);
    $avg_spent = ($visit_count > 0) ? ($total_spent / $visit_count) : 0;
    
    // Set the response data structure exactly as the frontend expects
    $response = [
        "status" => true,
        "message" => "تم استرجاع بيانات العميل بنجاح",
        "data" => [
            "customer" => $customer,
            "visits" => $visits,
            "favorite_services" => $favorite_services,
            "total_spent" => $total_spent,
            "avg_spent" => $avg_spent
        ]
    ];
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>