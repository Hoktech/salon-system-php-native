<?php
/**
 * ملف قراءة المنتجات
 * يستخدم للحصول على قائمة المنتجات
 */

// تعريف ثابت للتحقق من الوصول المباشر
define('SALON_SYSTEM', true);

// استيراد ملف الإعدادات
require_once '../config/config.php';

// استيراد ملف التحقق من الصلاحيات
require_once '../auth/permissions.php';

// التحقق من تسجيل الدخول والصلاحيات
checkApiPermission('view_products');

// إنشاء مصفوفة الاستجابة
$response = [
    'status' => false,
    'message' => 'حدث خطأ أثناء جلب بيانات المنتجات',
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
    $query = "SELECT * FROM products WHERE 1=1";
    $params = [];

    // إضافة شرط الفرع إذا كان المستخدم ليس مديرًا
    if ($_SESSION['role'] !== 'admin') {
        $query .= " AND branch_id = ?";
        $params[] = $branch_id;
    }

    // إضافة البحث إذا كان موجودًا
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = sanitizeInput($_GET['search']);
        $query .= " AND (name LIKE ? OR category LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    // فلترة حسب الفئة
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $category = sanitizeInput($_GET['category']);
        $query .= " AND category = ?";
        $params[] = $category;
    }

    // فلترة حسب الحالة (للبيع أو للاستخدام الداخلي)
    if (isset($_GET['for_sale']) && is_numeric($_GET['for_sale'])) {
        $for_sale = intval($_GET['for_sale']);
        $query .= " AND for_sale = ?";
        $params[] = $for_sale;
    }

    if (isset($_GET['for_internal_use']) && is_numeric($_GET['for_internal_use'])) {
        $for_internal_use = intval($_GET['for_internal_use']);
        $query .= " AND for_internal_use = ?";
        $params[] = $for_internal_use;
    }

    // فلترة حسب المخزون
    if (isset($_GET['low_stock']) && $_GET['low_stock'] == 1) {
        $query .= " AND stock_quantity <= minimum_quantity";
    }

    // إضافة ترتيب
    $query .= " ORDER BY name ASC";

    // إضافة الترقيم
    if (isset($_GET['page']) && is_numeric($_GET['page']) && isset($_GET['limit']) && is_numeric($_GET['limit'])) {
        $page = intval($_GET['page']);
        $limit = intval($_GET['limit']);
        $offset = ($page - 1) * $limit;
        
        // حساب إجمالي عدد المنتجات للترقيم
        $countQuery = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
        $totalProducts = $db->getValue($countQuery, $params);
        
        // إضافة حدود LIMIT للاستعلام الأصلي
        $query .= " LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
    }

    // تنفيذ الاستعلام
    $products = $db->getRows($query, $params);

    // إعداد الاستجابة
    $response['status'] = true;
    $response['message'] = 'تم جلب بيانات المنتجات بنجاح';
    $response['data'] = $products;
    
    // إضافة معلومات الترقيم إذا كانت متاحة
    if (isset($totalProducts)) {
        $response['total_records'] = $totalProducts;
        $response['total_pages'] = ceil($totalProducts / $limit);
        $response['current_page'] = $page;
    }
    
    // إضافة قائمة الفئات المتاحة
    $categoryQuery = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''";
    $categories = $db->getRows($categoryQuery);
    $categoryList = [];
    foreach ($categories as $cat) {
        $categoryList[] = $cat['category'];
    }
    $response['categories'] = $categoryList;
} catch (Exception $e) {
    $response['message'] = 'حدث خطأ غير متوقع: ' . $e->getMessage();
    // تسجيل الخطأ
    logError('خطأ في جلب بيانات المنتجات: ' . $e->getMessage());
}

// إرسال الاستجابة بتنسيق JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);