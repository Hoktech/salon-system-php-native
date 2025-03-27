<?php
// Headers
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=customers_export_" . date('Y-m-d') . ".csv");

// Include database and permission files
include_once '../config/database.php';
include_once '../config/helpers.php';
include_once '../auth/permissions.php';

try {
    // Check user permissions
    $user = checkUserPermission(['admin', 'manager', 'cashier']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize query parameters
    $params = [];
    $where_clauses = [];
    
    // Parse query parameters
    $gender = isset($_GET['gender']) ? sanitizeInput($_GET['gender']) : null;
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
    
    // Build query conditions
    if ($gender) {
        $where_clauses[] = "c.gender = :gender";
        $params[':gender'] = $gender;
    }
    
    if ($search) {
        $where_clauses[] = "(c.full_name LIKE :search OR c.phone LIKE :search OR c.email LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // For non-admin users, restrict to their branch only
    if (!in_array($user['role'], ['admin'])) {
        $where_clauses[] = "c.branch_id = :branch_id";
        $params[':branch_id'] = $user['branch_id'];
    } else if (isset($_GET['branch_id']) && !empty($_GET['branch_id'])) {
        $branch_id = (int)$_GET['branch_id'];
        $where_clauses[] = "c.branch_id = :branch_id";
        $params[':branch_id'] = $branch_id;
    }
    
    // Create WHERE clause
    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    // Build the SQL query with LEFT JOIN to get branch name
    $query = "
        SELECT c.id, c.full_name, c.phone, c.email, c.birthdate, c.gender, c.address, 
               c.notes, c.loyalty_points, c.created_at, b.name AS branch_name
        FROM customers c
        LEFT JOIN branches b ON c.branch_id = b.id
        $where_sql
        ORDER BY c.full_name ASC
    ";
    
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Create CSV file
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for proper Arabic display in Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Write headers
    fputcsv($output, [
        'معرّف',
        'الاسم الكامل',
        'الهاتف',
        'البريد الإلكتروني',
        'تاريخ الميلاد',
        'الجنس',
        'العنوان',
        'الملاحظات',
        'نقاط الولاء',
        'الفرع',
        'تاريخ التسجيل'
    ]);
    
    // Write data rows
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Format gender
        $gender = ($row['gender'] == 'male') ? 'ذكر' : 'أنثى';
        
        fputcsv($output, [
            $row['id'],
            $row['full_name'],
            $row['phone'],
            $row['email'] ?: '',
            $row['birthdate'] ?: '',
            $gender,
            $row['address'] ?: '',
            $row['notes'] ?: '',
            $row['loyalty_points'],
            $row['branch_name'] ?: '',
            $row['created_at']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    // In case of error, return JSON error
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "status" => false,
        "message" => $e->getMessage()
    ]);
}
?>