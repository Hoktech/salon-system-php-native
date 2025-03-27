<?php
/**
 * ملف قراءة العملاء
 * يستخدم للحصول على قائمة العملاء
 */

// تعريف ثابت للتحقق من الوصول المباشر
define('SALON_SYSTEM', true);

// استيراد ملف الإعدادات
require_once '../config/config.php';

// استيراد ملف التحقق من الصلاحيات
require_once '../auth/permissions.php';

// التحقق من تسجيل الدخول والصلاحيات
checkApiPermission('view_customers');

// إنشاء مصفوفة الاستجابة
$response = [
    'status' => false,
    'message' => 'حدث خطأ أثناء جلب بيانات العملاء',
    'data' => []
];

try {
    // التحقق من طريقة الطلب
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $response['message'] = 'طريقة الطلب غير مسموح بها';
        echo json_encode($response);
        exit;
    }

    // الحصول على معرف الفرع من الجلسة
    $branch_id = $_SESSION['branch_id'];

    // إعداد الاستعلام
    $query = "SELECT * FROM customers WHERE 1=1";
    $params = [];

    // إضافة شرط الفرع إذا كان المستخدم ليس مديرًا
    if ($_SESSION['role'] !== 'admin') {
        $query .= " AND branch_id = ?";
        $params[] = $branch_id;
    }

    // إضافة البحث إذا كان موجودًا
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = sanitizeInput($_GET['search']);
        $query .= " AND (full_name LIKE ? OR phone LIKE ? OR email LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    // إضافة فلترة حسب الجنس
    if (isset($_GET['gender']) && !empty($_GET['gender'])) {
        $gender = sanitizeInput($_GET['gender']);
        $query .= " AND gender = ?";
        $params[] = $gender;
    }

    // إضافة ترتيب
    $query .= " ORDER BY full_name ASC";

    // إضافة الترقيم
    if (isset($_GET['page']) && is_numeric($_GET['page']) && isset($_GET['limit']) && is_numeric($_GET['limit'])) {
        $page = intval($_GET['page']);
        $limit = intval($_GET['limit']);
        $offset = ($page - 1) * $limit;
        
        // حساب إجمالي عدد العملاء للترقيم
        $countQuery = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
        $totalCustomers = $db->getValue($countQuery, $params);
        
        // إضافة حدود LIMIT للاستعلام الأصلي
        $query .= " LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
    }

    // تنفيذ الاستعلام
    $customers = $db->getRows($query, $params);

    // إعداد الاستجابة
    $response['status'] = true;
    $response['message'] = 'تم جلب بيانات العملاء بنجاح';
    $response['data'] = $customers;
    
    // إضافة معلومات الترقيم إذا كانت متاحة
    if (isset($totalCustomers)) {
        $response['total_records'] = $totalCustomers;
        $response['total_pages'] = ceil($totalCustomers / $limit);
        $response['current_page'] = $page;
    }
} catch (Exception $e) {
    $response['message'] = 'حدث خطأ غير متوقع: ' . $e->getMessage();
    // تسجيل الخطأ
    logError('خطأ في جلب بيانات العملاء: ' . $e->getMessage());
}

// إرسال الاستجابة بتنسيق JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);