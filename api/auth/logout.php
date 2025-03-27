<?php
/**
 * ملف تسجيل الخروج
 * يستخدم لإنهاء الجلسة الحالية وتسجيل خروج المستخدم من النظام
 */

// تعريف ثابت للتحقق من الوصول المباشر
define('SALON_SYSTEM', true);

// التأكد من بدء الجلسة
session_start();

// إنشاء مصفوفة الاستجابة
$response = [
    'status' => false,
    'message' => 'حدث خطأ أثناء تسجيل الخروج'
];

try {
    // التأكد من أن الطلب بطريقة POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // التأكد من وجود جلسة مسجل دخول
        if (isset($_SESSION['user_id'])) {
            // تسجيل آخر تسجيل خروج للمستخدم في قاعدة البيانات
            // هذا اختياري ويمكن إضافته لاحقًا إذا لزم الأمر
            
            // حذف جميع متغيرات الجلسة
            $_SESSION = [];
            
            // حذف ملف تعريف الجلسة (كوكيز)
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            // تدمير الجلسة
            session_destroy();
            
            // تحديث الاستجابة بالنجاح
            $response['status'] = true;
            $response['message'] = 'تم تسجيل الخروج بنجاح';
        } else {
            // لا توجد جلسة مسجل دخول
            $response['message'] = 'أنت غير مسجل دخول بالفعل';
        }
    } else {
        // الطلب ليس بطريقة POST
        $response['message'] = 'طريقة الطلب غير مسموح بها';
    }
} catch (Exception $e) {
    // حدث خطأ أثناء تسجيل الخروج
    $response['message'] = 'حدث خطأ غير متوقع: ' . $e->getMessage();
}

// إرسال الاستجابة بتنسيق JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);