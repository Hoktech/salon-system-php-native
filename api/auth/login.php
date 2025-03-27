<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and helper files
include_once '../config/database.php';
include_once '../config/helpers.php';

// Initialize response array
$response = [
    "status" => false,
    "message" => "",
    "data" => null
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("طريقة طلب غير صالحة");
    }
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (!isset($data->username) || !isset($data->password)) {
        throw new Exception("يرجى إدخال اسم المستخدم وكلمة المرور");
    }
    
    // Sanitize input
    $username = sanitizeInput($data->username);
    $password = $data->password; // Do not sanitize password before verification
    
    // Check if username is empty
    if (empty($username)) {
        throw new Exception("يرجى إدخال اسم المستخدم");
    }
    
    // Check if password is empty
    if (empty($password)) {
        throw new Exception("يرجى إدخال كلمة المرور");
    }
    
    // Prepare query to check user
    $query = "
        SELECT id, username, password, full_name, role, branch_id, active, email, phone, created_at, last_login
        FROM users 
        WHERE username = :username
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    
    // Check if user exists
    if ($stmt->rowCount() === 0) {
        throw new Exception("اسم المستخدم أو كلمة المرور غير صحيحة");
    }
    
    // Get user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        // لتسهيل التطوير في بداية المشروع، نتحقق أيضًا إذا كانت كلمة المرور متطابقة كما هي
        if ($password !== $user['password']) {
            throw new Exception("اسم المستخدم أو كلمة المرور غير صحيحة");
        }
    }
    
    // Check if user is active
    if ($user['active'] !== 'active' && $user['active'] !== '1' && $user['active'] !== 1) {
        throw new Exception("حساب المستخدم غير نشط. يرجى التواصل مع المسؤول");
    }
    
    // Get branch info if user is assigned to a branch
    $branch = null;
    if ($user['branch_id']) {
        $branchQuery = "SELECT id, name, address, phone FROM branches WHERE id = :branch_id";
        $branchStmt = $db->prepare($branchQuery);
        $branchStmt->bindParam(":branch_id", $user['branch_id']);
        $branchStmt->execute();
        
        if ($branchStmt->rowCount() > 0) {
            $branch = $branchStmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    // Get user permissions based on role
    $permissions = getUserPermissions($user['role']);
    
    // Update last login timestamp
    $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(":id", $user['id']);
    $updateStmt->execute();
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['branch_id'] = $user['branch_id'];
    $_SESSION['permissions'] = $permissions;
    
    // Log login activity
    $logQuery = "
        INSERT INTO activity_logs (user_id, action, description, created_at)
        VALUES (:user_id, 'login', 'تسجيل دخول ناجح', NOW())
    ";
    
    try {
        $logStmt = $db->prepare($logQuery);
        $logStmt->bindParam(":user_id", $user['id']);
        $logStmt->execute();
    } catch (Exception $e) {
        // Ignore log errors, continue with login
    }
    
    // Prepare user data for response (exclude password)
    $userData = [
        "id" => $user['id'],
        "username" => $user['username'],
        "name" => $user['full_name'],
        "role" => $user['role'],
        "email" => $user['email'],
        "phone" => $user['phone'],
        "active" => $user['active'],
        "branch" => $branch,
        "permissions" => $permissions,
        "last_login" => $user['last_login']
    ];
    
    // Set success response
    $response['status'] = true;
    $response['message'] = "تم تسجيل الدخول بنجاح";
    $response['data'] = $userData;
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);

/**
 * Get user permissions based on role
 * 
 * @param string $role User role
 * @return array Permissions
 */
function getUserPermissions($role) {
    $permissions = [];
    
    switch ($role) {
        case 'admin':
            $permissions = [
                'users_view', 'users_add', 'users_edit', 'users_delete',
                'branches_view', 'branches_add', 'branches_edit', 'branches_delete',
                'customers_view', 'customers_add', 'customers_edit', 'customers_delete',
                'services_view', 'services_add', 'services_edit', 'services_delete',
                'products_view', 'products_add', 'products_edit', 'products_delete',
                'appointments_view', 'appointments_add', 'appointments_edit', 'appointments_delete',
                'invoices_view', 'invoices_add', 'invoices_edit', 'invoices_delete',
                'expenses_view', 'expenses_add', 'expenses_edit', 'expenses_delete',
                'employees_view', 'employees_add', 'employees_edit', 'employees_delete',
                'reports_view', 'settings_edit'
            ];
            break;
            
        case 'manager':
            $permissions = [
                'customers_view', 'customers_add', 'customers_edit', 
                'services_view', 
                'products_view', 
                'appointments_view', 'appointments_add', 'appointments_edit', 'appointments_delete',
                'invoices_view', 'invoices_add', 'invoices_edit',
                'expenses_view', 'expenses_add', 'expenses_edit',
                'employees_view', 'employees_edit',
                'reports_view'
            ];
            break;
            
        case 'cashier':
            $permissions = [
                'customers_view', 'customers_add', 'customers_edit',
                'services_view',
                'products_view',
                'appointments_view', 'appointments_add', 'appointments_edit',
                'invoices_view', 'invoices_add', 'invoices_edit',
                'expenses_view'
            ];
            break;
            
        case 'employee':
            $permissions = [
                'customers_view',
                'services_view',
                'products_view',
                'appointments_view',
                'invoices_view'
            ];
            break;
    }
    
    return $permissions;
}
?>