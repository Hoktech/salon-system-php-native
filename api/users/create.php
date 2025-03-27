<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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
    "data" => null
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("طريقة طلب غير صالحة");
    }
    
    // Check user permissions
    $user = checkUserPermission(['admin', 'manager']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (!isset($data->username) || empty($data->username) ||
        !isset($data->password) || empty($data->password) ||
        !isset($data->full_name) || empty($data->full_name) ||
        !isset($data->role) || empty($data->role)) {
        throw new Exception("اسم المستخدم، كلمة المرور، الاسم الكامل والدور مطلوبان");
    }
    
    // Sanitize and validate input
    $username = sanitizeInput($data->username);
    $password = $data->password; // Will be hashed later
    $full_name = sanitizeInput($data->full_name);
    $email = isset($data->email) ? sanitizeInput($data->email) : null;
    $phone = isset($data->phone) ? sanitizeInput($data->phone) : null;
    $role = sanitizeInput($data->role);
    $branch_id = isset($data->branch_id) ? (int)$data->branch_id : $user['branch_id'];
    $active = isset($data->active) ? (int)$data->active : 1;
    
    // Non-admin users can only create users for their own branch
    if ($user['role'] !== 'admin' && $branch_id != $user['branch_id']) {
        throw new Exception("ليس لديك صلاحية لإنشاء مستخدمين في فروع أخرى");
    }
    
    // Non-admin users cannot create admin users
    if ($user['role'] !== 'admin' && $role === 'admin') {
        throw new Exception("ليس لديك صلاحية لإنشاء مستخدمين بدور مدير النظام");
    }
    
    // Check if username already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception("اسم المستخدم موجود بالفعل");
    }
    
    // Check if email already exists (if provided)
    if ($email) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("البريد الإلكتروني مستخدم بالفعل");
        }
    }
    
    // Check if branch exists (if provided)
    if ($branch_id) {
        $stmt = $db->prepare("SELECT id FROM branches WHERE id = :id");
        $stmt->bindParam(":id", $branch_id);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("الفرع غير موجود");
        }
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert the user
    $query = "
        INSERT INTO users (
            username, password, full_name, email, phone, role, 
            branch_id, active, created_at
        ) VALUES (
            :username, :password, :full_name, :email, :phone, :role, 
            :branch_id, :active, NOW()
        )
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam(":full_name", $full_name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":branch_id", $branch_id);
    $stmt->bindParam(":active", $active);
    
    if ($stmt->execute()) {
        $user_id = $db->lastInsertId();
        
        // Get the user details for response
        $query = "
            SELECT u.id, u.username, u.full_name, u.email, u.phone, u.role, 
                   u.branch_id, u.active, u.created_at,
                   b.name as branch_name 
            FROM users u
            LEFT JOIN branches b ON u.branch_id = b.id
            WHERE u.id = :id
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Remove password from response
        unset($userData['password']);
        
        // Set default permissions based on role
        setDefaultPermissions($db, $user_id, $role);
        
        $response['status'] = "success";
        $response['message'] = "تم إنشاء المستخدم بنجاح";
        $response['data'] = $userData;
    } else {
        throw new Exception("حدث خطأ أثناء إنشاء المستخدم");
    }
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);

/**
 * Set default permissions for a user based on role
 */
function setDefaultPermissions($db, $user_id, $role) {
    // Get permission IDs based on role
    $permissions = [];
    
    switch ($role) {
        case 'admin':
            // Admin has all permissions, no need to add specific ones
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
    
    // Insert permissions
    if (!empty($permissions)) {
        // Get permission IDs
        $placeholders = str_repeat("?,", count($permissions) - 1) . "?";
        $stmt = $db->prepare("SELECT id, name FROM permissions WHERE name IN ($placeholders)");
        
        foreach ($permissions as $i => $permission) {
            $stmt->bindValue($i + 1, $permission);
        }
        
        $stmt->execute();
        $permissionRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Insert user permissions
        if (!empty($permissionRows)) {
            $insertQuery = "INSERT INTO user_permissions (user_id, permission_id) VALUES (:user_id, :permission_id)";
            $insertStmt = $db->prepare($insertQuery);
            
            foreach ($permissionRows as $permission) {
                $insertStmt->bindParam(":user_id", $user_id);
                $insertStmt->bindParam(":permission_id", $permission['id']);
                $insertStmt->execute();
            }
        }
    }
}