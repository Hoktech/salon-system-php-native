<?php
/**
 * ملف إنشاء عميل جديد
 * يستخدم لإضافة عميل جديد إلى قاعدة البيانات
 */

// تعريف ثابت للتحقق من الوصول المباشر
define('SALON_SYSTEM', true);

// استيراد ملف الإعدادات
require_once '../config/config.php';

// استيراد ملف التحقق من الصلاحيات
require_once '../auth/permissions.php';

// التحقق من تسجيل الدخول والصلاحيات
checkApiPermission('add_customers');

// إنشاء مصفوفة الاستجابة
$response = [
    'status' => false,
    'message' => 'حدث خطأ أثناء إضافة العميل',
    'data' => null
];

try {
    // التحقق من طريقة الطلب
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'طريقة الطلب غير مسموح بها';
        echo json_encode($response);
        exit;
    }

    // الحصول على البيانات المرسلة
    $data = json_decode(file_get_contents('php://input'), true);

    // التحقق من وجود البيانات المطلوبة
    if (!isset($data['full_name']) || empty($data['full_name']) || !isset($data['phone']) || empty($data['phone'])) {
        $response['message'] = 'اسم العميل ورقم الهاتف مطلوبان';
        echo json_encode($response);
        exit;
    }

    // تنظيف البيانات
    $customerData = [
        'full_name' => sanitizeInput($data['full_name']),
        'phone' => sanitizeInput($data['phone']),
        'email' => isset($data['email']) ? sanitizeInput($data['email']) : null,
        'birthdate' => isset($data['birthdate']) && !empty($data['birthdate']) ? $data['birthdate'] : null,
        'gender' => isset($data['gender']) ? sanitizeInput($data['gender']) : 'male',
        'address' => isset($data['address']) ? sanitizeInput($data['address']) : null,
        'notes' => isset($data['notes']) ? sanitizeInput($data['notes']) : null,
        'branch_id' => $_SESSION['branch_id'],
        'created_at' => date('Y-m-d H:i:s')
    ];

    // التحقق من عدم وجود رقم هاتف مكرر
    $query = "SELECT id FROM customers WHERE phone = ? AND branch_id = ?";
    $existingCustomer = $db->getRow($query, [$customerData['phone'], $customerData['branch_id']]);
    
    if ($existingCustomer) {
        $response['message'] = 'رقم الهاتف مسجل مسبقا لعميل آخر';
        echo json_encode($response);
        exit;
    }

    // إدراج البيانات في قاعدة البيانات
    $customerId = $db->insert('customers', $customerData);

    if ($customerId) {
        // جلب بيانات العميل بعد الإضافة
        $query = "SELECT * FROM customers WHERE id = ?";
        $customer = $db->getRow($query, [$customerId]);

        // إعداد الاستجابة
        $response['status'] = true;
        $response['message'] = 'تم إضافة العميل بنجاح';
        $response['data'] = $customer;
    } else {
        $response['message'] = 'فشل في إضافة العميل. الرجاء المحاولة مرة أخرى';
    }
} catch (Exception $e) {
    $response['message'] = 'حدث خطأ غير متوقع: ' . $e->getMessage();
    // تسجيل الخطأ
    logError('خطأ في إضافة عميل جديد: ' . $e->getMessage());
}

// إرسال الاستجابة بتنسيق JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);