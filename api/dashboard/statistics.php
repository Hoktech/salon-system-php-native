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
    $user = checkUserPermission(); // any logged in user can view dashboard
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get period parameter (default: month)
    $period = isset($_GET['period']) ? sanitizeInput($_GET['period']) : 'month';
    
    // Define date range based on period
    switch ($period) {
        case 'week':
            $startDate = date('Y-m-d', strtotime('-1 week'));
            $endDate = date('Y-m-d');
            $groupBy = 'DATE(created_at)';
            $dateFormat = 'Y-m-d';
            break;
        case 'month':
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $groupBy = 'DATE(created_at)';
            $dateFormat = 'Y-m-d';
            break;
        case 'year':
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $groupBy = 'MONTH(created_at)';
            $dateFormat = 'Y-m';
            break;
        default:
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $groupBy = 'DATE(created_at)';
            $dateFormat = 'Y-m-d';
            break;
    }
    
    // For branch restrictions
    $branchCondition = "";
    $branchParams = [];
    if ($user['role'] !== 'admin' && $user['branch_id']) {
        $branchCondition = " AND branch_id = :branch_id";
        $branchParams[':branch_id'] = $user['branch_id'];
    }
    
    // Initialize statistics array
    $statistics = [
        "period" => [
            "start" => $startDate,
            "end" => $endDate,
            "type" => $period
        ],
        "revenue" => [
            "total" => 0,
            "services" => 0,
            "products" => 0,
            "daily" => []
        ],
        "expenses" => [
            "total" => 0,
            "categories" => [],
            "daily" => []
        ],
        "appointments" => [
            "total" => 0,
            "completed" => 0,
            "cancelled" => 0,
            "no_show" => 0,
            "daily" => []
        ],
        "customers" => [
            "total" => 0,
            "new" => 0,
            "returning" => 0
        ],
        "services" => [
            "most_popular" => []
        ],
        "employees" => [
            "performance" => []
        ]
    ];
    
    // Get total revenue in period
    $query = "
        SELECT 
            COALESCE(SUM(final_amount), 0) as total,
            COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN final_amount ELSE 0 END), 0) as cash,
            COALESCE(SUM(CASE WHEN payment_method = 'card' THEN final_amount ELSE 0 END), 0) as card,
            COALESCE(SUM(CASE WHEN payment_method = 'bank_transfer' THEN final_amount ELSE 0 END), 0) as bank_transfer,
            COALESCE(SUM(CASE WHEN payment_method = 'other' THEN final_amount ELSE 0 END), 0) as other
        FROM invoices
        WHERE payment_status = 'paid'
        AND invoice_date BETWEEN :start_date AND :end_date
        $branchCondition
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $statistics['revenue']['total'] = (float)$result['total'];
    $statistics['revenue']['payment_methods'] = [
        ["method" => "نقدي", "amount" => (float)$result['cash']],
        ["method" => "بطاقة", "amount" => (float)$result['card']],
        ["method" => "تحويل بنكي", "amount" => (float)$result['bank_transfer']],
        ["method" => "أخرى", "amount" => (float)$result['other']]
    ];
    
    // Get revenue from services and products
    $query = "
        SELECT 
            COALESCE(SUM(is2.total), 0) as services_total
        FROM invoice_services is2
        JOIN invoices i ON is2.invoice_id = i.id
        WHERE i.payment_status = 'paid'
        AND i.invoice_date BETWEEN :start_date AND :end_date
        $branchCondition
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $statistics['revenue']['services'] = (float)$result['services_total'];
    
    $query = "
        SELECT 
            COALESCE(SUM(ip.total), 0) as products_total
        FROM invoice_products ip
        JOIN invoices i ON ip.invoice_id = i.id
        WHERE i.payment_status = 'paid'
        AND i.invoice_date BETWEEN :start_date AND :end_date
        $branchCondition
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $statistics['revenue']['products'] = (float)$result['products_total'];
    
    // Get daily revenue
    $query = "
        SELECT 
            DATE(invoice_date) as date,
            COALESCE(SUM(final_amount), 0) as total
        FROM invoices
        WHERE payment_status = 'paid'
        AND invoice_date BETWEEN :start_date AND :end_date
        $branchCondition
        GROUP BY DATE(invoice_date)
        ORDER BY date ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    
    $dailyRevenue = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dailyRevenue[] = [
            "date" => $row['date'],
            "total" => (float)$row['total']
        ];
    }
    $statistics['revenue']['daily'] = $dailyRevenue;
    
    // Get total expenses in period
    $query = "
        SELECT 
            COALESCE(SUM(amount), 0) as total
        FROM expenses
        WHERE date BETWEEN :start_date AND :end_date
        $branchCondition
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $statistics['expenses']['total'] = (float)$result['total'];
    
    // Get expenses by category
    $query = "
        SELECT 
            category,
            COALESCE(SUM(amount), 0) as total
        FROM expenses
        WHERE date BETWEEN :start_date AND :end_date
        $branchCondition
        GROUP BY category
        ORDER BY total DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    
    $expenseCategories = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $expenseCategories[] = [
            "category" => $row['category'],
            "amount" => (float)$row['total']
        ];
    }
    $statistics['expenses']['categories'] = $expenseCategories;
    
    // Get daily expenses
    $query = "
        SELECT 
            DATE(date) as date,
            COALESCE(SUM(amount), 0) as total
        FROM expenses
        WHERE date BETWEEN :start_date AND :end_date
        $branchCondition
        GROUP BY DATE(date)
        ORDER BY date ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    
    $dailyExpenses = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dailyExpenses[] = [
            "date" => $row['date'],
            "total" => (float)$row['total']
        ];
    }
    $statistics['expenses']['daily'] = $dailyExpenses;
    
    // Get appointment statistics
    $query = "
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN status = 'no-show' THEN 1 ELSE 0 END) as no_show
        FROM appointments
        WHERE date BETWEEN :start_date AND :end_date
        $branchCondition
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $statistics['appointments']['total'] = (int)$result['total'];
    $statistics['appointments']['completed'] = (int)$result['completed'];
    $statistics['appointments']['cancelled'] = (int)$result['cancelled'];
    $statistics['appointments']['no_show'] = (int)$result['no_show'];
    
    // Get daily appointments
    $query = "
        SELECT 
            date,
            COUNT(*) as total
        FROM appointments
        WHERE date BETWEEN :start_date AND :end_date
        $branchCondition
        GROUP BY date
        ORDER BY date ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    
    $dailyAppointments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dailyAppointments[] = [
            "date" => $row['date'],
            "total" => (int)$row['total']
        ];
    }
    $statistics['appointments']['daily'] = $dailyAppointments;
    
    // Get customer statistics
    $query = "
        SELECT 
            COUNT(DISTINCT c.id) as total,
            SUM(CASE WHEN c.created_at BETWEEN :start_date AND :end_date THEN 1 ELSE 0 END) as new_customers
        FROM customers c
        LEFT JOIN invoices i ON c.id = i.customer_id
        WHERE (i.invoice_date BETWEEN :start_date AND :end_date OR c.created_at BETWEEN :start_date AND :end_date)
        AND (i.id IS NOT NULL OR c.created_at BETWEEN :start_date AND :end_date)
        $branchCondition
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $statistics['customers']['total'] = (int)$result['total'];
    $statistics['customers']['new'] = (int)$result['new_customers'];
    $statistics['customers']['returning'] = $statistics['customers']['total'] - $statistics['customers']['new'];
    
    // Get most popular services
    $query = "
        SELECT 
            s.id,
            s.name,
            COUNT(is2.id) as count,
            COALESCE(SUM(is2.total), 0) as revenue
        FROM services s
        JOIN invoice_services is2 ON s.id = is2.service_id
        JOIN invoices i ON is2.invoice_id = i.id
        WHERE i.payment_status = 'paid'
        AND i.invoice_date BETWEEN :start_date AND :end_date
        $branchCondition
        GROUP BY s.id
        ORDER BY count DESC
        LIMIT 5
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    
    $popularServices = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $popularServices[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "count" => (int)$row['count'],
            "revenue" => (float)$row['revenue']
        ];
    }
    $statistics['services']['most_popular'] = $popularServices;
    
    // Get employee performance
    $query = "
        SELECT 
            u.id,
            u.full_name,
            COUNT(is2.id) as services_count,
            COALESCE(SUM(is2.total), 0) as revenue
        FROM users u
        JOIN invoice_services is2 ON u.id = is2.employee_id
        JOIN invoices i ON is2.invoice_id = i.id
        WHERE i.payment_status = 'paid'
        AND i.invoice_date BETWEEN :start_date AND :end_date
        AND u.role = 'employee'
        $branchCondition
        GROUP BY u.id
        ORDER BY revenue DESC
        LIMIT 5
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    foreach ($branchParams as $key => $value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    
    $employeePerformance = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $employeePerformance[] = [
            "id" => $row['id'],
            "name" => $row['full_name'],
            "services_count" => (int)$row['services_count'],
            "revenue" => (float)$row['revenue']
        ];
    }
    $statistics['employees']['performance'] = $employeePerformance;
    
    // Set response
    $response['status'] = true;
    $response['message'] = "تم استرجاع الإحصائيات بنجاح";
    $response['data'] = $statistics;
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>