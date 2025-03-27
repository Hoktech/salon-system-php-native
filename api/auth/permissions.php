<?php
/**
 * ملف التحقق من صلاحيات المستخدم
 * يستخدم للتحقق من أن المستخدم لديه صلاحية للوصول إلى الصفحة أو API
 */

// التحقق من وجود جلسة نشطة وبدء الجلسة إذا لم تكن موجودة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * التحقق من تسجيل دخول المستخدم وصلاحياته
 * 
 * @param array $allowedRoles الأدوار المسموح لها بالوصول
 * @return array بيانات المستخدم
 * @throws Exception في حالة عدم وجود صلاحية
 */
function checkUserPermission($allowedRoles = []) {
    // التحقق من تسجيل دخول المستخدم
    if (!isset($_SESSION['user_id'])) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
            'data' => null
        ]);
        exit;
    }
    
    // التحقق من الأدوار المسموح لها
    if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode([
            'status' => false,
            'message' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة',
            'data' => null
        ]);
        exit;
    }
    
    // إرجاع بيانات المستخدم
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'name' => $_SESSION['name'],
        'role' => $_SESSION['role'],
        'branch_id' => $_SESSION['branch_id'] ?? null,
        'permissions' => $_SESSION['permissions'] ?? []
    ];
}

/**
 * التحقق من صلاحية محددة للمستخدم
 * 
 * @param string|array $requiredPermissions الصلاحية أو الصلاحيات المطلوبة
 * @return bool نتيجة التحقق
 */
function checkSpecificPermission($requiredPermissions) {
    // التحقق من تسجيل دخول المستخدم
    if (!isset($_SESSION['user_id'])) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
            'data' => null
        ]);
        exit;
    }
    
    // الصلاحيات للمستخدم الحالي
    $userPermissions = $_SESSION['permissions'] ?? [];
    
    // إذا كان المستخدم مدير (admin) فلديه جميع الصلاحيات
    if ($_SESSION['role'] === 'admin') {
        return true;
    }
    
    // إذا كانت الصلاحية المطلوبة مصفوفة، نتحقق من وجود واحدة على الأقل
    if (is_array($requiredPermissions)) {
        $hasPermission = false;
        foreach ($requiredPermissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                $hasPermission = true;
                break;
            }
        }
        
        if (!$hasPermission) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode([
                'status' => false,
                'message' => 'ليس لديك صلاحية للقيام بهذه العملية',
                'data' => null
            ]);
            exit;
        }
    } else {
        // التحقق من وجود الصلاحية المطلوبة
        if (!in_array($requiredPermissions, $userPermissions)) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode([
                'status' => false,
                'message' => 'ليس لديك صلاحية للقيام بهذه العملية',
                'data' => null
            ]);
            exit;
        }
    }
    
    return true;
}

/**
 * التحقق من صلاحية الوصول إلى بيانات فرع معين
 * 
 * @param int $branchId معرف الفرع المطلوب الوصول إليه
 * @return bool نتيجة التحقق
 */
function checkBranchAccess($branchId) {
    // المدير (admin) لديه صلاحية الوصول إلى كل الفروع
    if ($_SESSION['role'] === 'admin') {
        return true;
    }
    
    // باقي الأدوار يمكنها الوصول فقط لفروعها
    if ($_SESSION['branch_id'] != $branchId) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode([
            'status' => false,
            'message' => 'ليس لديك صلاحية للوصول إلى بيانات هذا الفرع',
            'data' => null
        ]);
        exit;
    }
    
    return true;
}

/**
 * الحصول على قائمة الصلاحيات حسب الدور
 * 
 * @param string $role دور المستخدم
 * @return array قائمة الصلاحيات
 */
function getRolePermissions($role) {
    $permissions = [];
    
    switch ($role) {
        case 'admin':
            // المدير لديه جميع الصلاحيات
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
            // مدير الفرع لديه صلاحيات إدارة الفرع
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
            // أمين الصندوق لديه صلاحيات محدودة
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
            // الموظف لديه صلاحيات للاطلاع فقط
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

/**
 * تسجيل حدث في سجل النظام
 * 
 * @param string $action نوع العملية
 * @param string $description وصف العملية
 * @param string $module الوحدة
 * @return bool نتيجة التسجيل
 */
function logUserActivity($action, $description, $module = '') {
    // التحقق من تسجيل دخول المستخدم
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "
            INSERT INTO activity_logs (user_id, action, description, module, ip_address, created_at)
            VALUES (:user_id, :action, :description, :module, :ip_address, NOW())
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":user_id", $_SESSION['user_id']);
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":module", $module);
        $stmt->bindParam(":ip_address", $_SERVER['REMOTE_ADDR']);
        
        return $stmt->execute();
    } catch (Exception $e) {
        // في حالة حدوث خطأ، لا نريد تعطيل النظام
        return false;
    }
}
?>