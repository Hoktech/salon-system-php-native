<?php
/**
 * ملف إنشاء خدمة جديدة
 * يستخدم لإضافة خدمة جديدة إلى قاعدة البيانات
 */

// تعريف ثابت للتحقق من الوصول المباشر
define('SALON_SYSTEM', true);

// استيراد ملف الإعدادات
require_once '../config/config.php';

// استيراد ملف التحقق من الصلاحيات
require_once '../auth/permissions.php';

// التحقق من تسجيل الدخول والصلاحيات
checkApiPermission('add_services');

// إنشاء مصفوفة الاستجابة
$response = [
    'status' => false,
    'message' => 'حدث خطأ أثناء إضافة الخدمة',
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
    if (!isset($data['name']) || empty($data['name']) || !isset($data['price']) || !is_numeric($data['price'])) {
        $response['message'] = 'اسم الخدمة والسعر مطلوبان';
        echo json_encode($response);
        exit;
    }

    // تنظيف البيانات
    $serviceData = [
        'name' => sanitizeInput($data['name']),
        'price' => floatval($data['price']),
        'category' => isset($data['category']) ? sanitizeInput($data['category']) : null,
        'duration' => isset($data['duration']) && is_numeric($data['duration']) ? intval($data['duration']) : 30,
        'description' => isset($data['description']) ? sanitizeInput($data['description']) : null,
        'active' => isset($data['active']) ? intval($data['active']) : 1,
        'branch_id' => $_SESSION['branch_id']
    ];

    // التحقق من عدم وجود خدمة بنفس الاسم في نفس الفرع
    $query = "SELECT id FROM services WHERE name = ? AND branch_id = ?";
    $existingService = $db->getRow($query, [$serviceData['name'], $serviceData['branch_id']]);
    
    if ($existingService) {
        $response['message'] = 'يوجد خدمة بنفس الاسم في هذا الفرع';
        echo json_encode($response);
        exit;
    }

    // إدراج البيانات في قاعدة البيانات
    $serviceId = $db->insert('services', $serviceData);

    if ($serviceId) {
        // إضافة الموظفين المتخصصين للخدمة إذا كانوا متوفرين
        if (isset($data['employees']) && is_array($data['employees']) && !empty($data['employees'])) {
            foreach ($data['employees'] as $employeeId) {
                // التحقق من وجود الموظف وأنه ينتمي للفرع
                $query = "SELECT id FROM users WHERE id = ? AND branch_id = ? AND role = 'employee'";
                $employee = $db->getRow($query, [$employeeId, $_SESSION['branch_id']]);
                
                if ($employee) {
                    $employeeServiceData = [
                        'employee_id' => $employeeId,
                        'service_id' => $serviceId
                    ];
                    $db->insert('employee_services', $employeeServiceData);
                }
            }
        }

        // جلب بيانات الخدمة بعد الإضافة
        $query = "SELECT * FROM services WHERE id = ?";
        $service = $db->getRow($query, [$serviceId]);

        // إعداد الاستجابة
        $response['status'] = true;
        $response['message'] = 'تم إضافة الخدمة بنجاح';
        $response['data'] = $service;
    } else {
        $response['message'] = 'فشل في إضافة الخدمة. الرجاء المحاولة مرة أخرى';
    }
} catch (Exception $e) {
    $response['message'] = 'حدث خطأ غير متوقع: ' . $e->getMessage();
    // تسجيل الخطأ
    logError('خطأ في إضافة خدمة جديدة: ' . $e->getMessage());
}

// إرسال الاستجابة بتنسيق JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);