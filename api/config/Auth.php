<?php
/**
 * فئة المصادقة
 * تستخدم لإدارة تسجيل الدخول والصلاحيات
 */
class Auth {
    private $db;
    private static $instance = null;

    /**
     * الحصول على نسخة واحدة من الفئة (Singleton Pattern)
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Auth();
        }
        return self::$instance;
    }

    /**
     * الإنشاء
     */
    private function __construct() {
        $this->db = Database::getInstance();
        
        // بدء الجلسة إذا لم تكن قد بدأت بالفعل
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * التحقق من صحة بيانات المستخدم
     * 
     * @param string $username اسم المستخدم
     * @param string $password كلمة المرور
     * @return bool هل البيانات صحيحة أم لا
     */
    public function login($username, $password) {
        $query = "SELECT id, username, password, full_name, role, branch_id, active FROM users WHERE username = ?";
        $user = $this->db->getRow($query, [$username]);
        
        if ($user && $user['active'] && password_verify($password, $user['password'])) {
            // تسجيل معلومات المستخدم في الجلسة
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['branch_id'] = $user['branch_id'];
            
            // تحديث وقت آخر تسجيل دخول
            $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], ['id' => $user['id']]);
            
            // تحميل صلاحيات المستخدم
            $this->loadPermissions($user['id']);
            
            return true;
        }
        
        return false;
    }

    /**
     * تحميل صلاحيات المستخدم
     * 
     * @param int $userId معرف المستخدم
     */
    private function loadPermissions($userId) {
        $query = "SELECT p.name FROM permissions p 
                  INNER JOIN user_permissions up ON p.id = up.permission_id 
                  WHERE up.user_id = ?";
        
        $permissions = $this->db->getRows($query, [$userId]);
        
        $_SESSION['permissions'] = [];
        foreach ($permissions as $permission) {
            $_SESSION['permissions'][] = $permission['name'];
        }
    }

    /**
     * تسجيل الخروج
     */
    public function logout() {
        // حذف معلومات المستخدم من الجلسة
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['full_name']);
        unset($_SESSION['role']);
        unset($_SESSION['branch_id']);
        unset($_SESSION['permissions']);
        
        // إنهاء الجلسة
        session_destroy();
    }

    /**
     * التحقق من حالة تسجيل الدخول
     * 
     * @return bool هل المستخدم مسجل الدخول أم لا
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * التحقق من الصلاحية
     * 
     * @param string $permission الصلاحية المطلوبة
     * @return bool هل المستخدم يملك الصلاحية أم لا
     */
    public function hasPermission($permission) {
        // المدير يملك كل الصلاحيات
        if ($_SESSION['role'] == 'admin') {
            return true;
        }
        
        // التحقق من وجود الصلاحية لدى المستخدم
        return in_array($permission, $_SESSION['permissions']);
    }

    /**
     * الحصول على معرف المستخدم الحالي
     * 
     * @return int معرف المستخدم
     */
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * الحصول على معرف الفرع الحالي
     * 
     * @return int معرف الفرع
     */
    public function getBranchId() {
        return $_SESSION['branch_id'] ?? null;
    }

    /**
     * الحصول على دور المستخدم
     * 
     * @return string دور المستخدم
     */
    public function getUserRole() {
        return $_SESSION['role'] ?? null;
    }

    /**
     * الحصول على اسم المستخدم الكامل
     * 
     * @return string اسم المستخدم الكامل
     */
    public function getFullName() {
        return $_SESSION['full_name'] ?? null;
    }

    /**
     * تشفير كلمة المرور
     * 
     * @param string $password كلمة المرور
     * @return string كلمة المرور المشفرة
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * إضافة صلاحية للمستخدم
     * 
     * @param int $userId معرف المستخدم
     * @param int $permissionId معرف الصلاحية
     * @return bool نتيجة العملية
     */
    public function addPermission($userId, $permissionId) {
        return $this->db->insert('user_permissions', [
            'user_id' => $userId,
            'permission_id' => $permissionId
        ]);
    }

    /**
     * حذف صلاحية من المستخدم
     * 
     * @param int $userId معرف المستخدم
     * @param int $permissionId معرف الصلاحية
     * @return int عدد الصفوف المتأثرة
     */
    public function removePermission($userId, $permissionId) {
        return $this->db->delete('user_permissions', [
            'user_id' => $userId,
            'permission_id' => $permissionId
        ]);
    }

    /**
     * الحصول على قائمة الصلاحيات للمستخدم
     * 
     * @param int $userId معرف المستخدم
     * @return array قائمة الصلاحيات
     */
    public function getUserPermissions($userId) {
        $query = "SELECT p.id, p.name, p.description FROM permissions p 
                  INNER JOIN user_permissions up ON p.id = up.permission_id 
                  WHERE up.user_id = ?";
        
        return $this->db->getRows($query, [$userId]);
    }
}