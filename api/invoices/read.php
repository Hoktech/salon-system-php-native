<?php
/**
 * ملف قراءة الفواتير
 * يستخدم للحصول على قائمة الفواتير حسب المعايير والفلاتر المختلفة
 */

// تعريف ثابت للتحقق من الوصول المباشر
define('SALON_SYSTEM', true);

// استيراد ملف الإعدادات
require_once '../config/config.php';

// استيراد ملف التحقق من الصلاحيات
require_once '../auth/permissions.php';

// التحقق من تسجيل الدخول والصلاحيات
checkApiPermission('view_invoices');

// إنشاء مصفوفة الاستجابة
$response = [
    'status' => false,
    'message' => 'حدث خطأ أثناء قراءة الفواتير',
    'data' => null
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
    $user_role = $_SESSION['role'];

    // بناء الاستعلام الأساسي
    $query = "SELECT i.*, 
                     c.full_name as customer_name, 
                     u.full_name as cashier_name, 
                     b.name as branch_name
              FROM invoices i
              LEFT JOIN customers c ON i.customer_id = c.id
              LEFT JOIN users u ON i.cashier_id = u.id
              LEFT JOIN branches b ON i.branch_id = b.id
              WHERE 1=1";
    $params = [];

    // إضافة شرط الفرع إذا كان المستخدم ليس مديرًا
    if ($user_role !== 'admin') {
        $query .= " AND i.branch_id = ?";
        $params[] = $branch_id;
    } else if (isset($_GET['branch_id']) && !empty($_GET['branch_id'])) {
        // السماح للمدير بتصفية حسب الفرع
        $branch_filter = intval($_GET['branch_id']);
        $query .= " AND i.branch_id = ?";
        $params[] = $branch_filter;
    }

    // فلترة حسب حالة إغلاق اليوم
    if (isset($_GET['day_end_closed']) && $_GET['day_end_closed'] !== '') {
        $day_end_closed = intval($_GET['day_end_closed']);
        $query .= " AND i.day_end_closed = ?";
        $params[] = $day_end_closed;
    }

    // فلترة حسب العميل
    if (isset($_GET['customer_id']) && !empty($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
        $query .= " AND i.customer_id = ?";
        $params[] = $customer_id;
    }

    // فلترة حسب رقم الفاتورة
    if (isset($_GET['invoice_number']) && !empty($_GET['invoice_number'])) {
        $invoice_number = sanitizeInput($_GET['invoice_number']);
        $query .= " AND i.invoice_number LIKE ?";
        $params[] = "%{$invoice_number}%";
    }

    // فلترة حسب تاريخ الفاتورة
    if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
        $start_date = sanitizeInput($_GET['start_date']);
        $query .= " AND DATE(i.invoice_date) >= ?";
        $params[] = $start_date;
    }

    if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
        $end_date = sanitizeInput($_GET['end_date']);
        $query .= " AND DATE(i.invoice_date) <= ?";
        $params[] = $end_date;
    }

    // فلترة حسب طريقة الدفع
    if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
        $payment_method = sanitizeInput($_GET['payment_method']);
        $query .= " AND i.payment_method = ?";
        $params[] = $payment_method;
    }

    // فلترة حسب حالة الدفع
    if (isset($_GET['payment_status']) && !empty($_GET['payment_status'])) {
        $payment_status = sanitizeInput($_GET['payment_status']);
        $query .= " AND i.payment_status = ?";
        $params[] = $payment_status;
    }

    // فلترة حسب الكاشير
    if (isset($_GET['cashier_id']) && !empty($_GET['cashier_id'])) {
        $cashier_id = intval($_GET['cashier_id']);
        $query .= " AND i.cashier_id = ?";
        $params[] = $cashier_id;
    }

    // ترتيب النتائج
    $query .= " ORDER BY i.invoice_date DESC";

    // إضافة الترقيم (البيجينيشن)
    if (isset($_GET['page']) && is_numeric($_GET['page']) && isset($_GET['limit']) && is_numeric($_GET['limit'])) {
        $page = intval($_GET['page']);
        $limit = intval($_GET['limit']);
        
        // التأكد من أن الصفحة والحد قيم إيجابية
        $page = max(1, $page);
        $limit = max(1, $limit);
        
        // حساب الإزاحة (offset)
        $offset = ($page - 1) * $limit;
        
        // استعلام لحساب إجمالي عدد السجلات
        $countQuery = preg_replace('/SELECT.*?FROM/i', 'SELECT COUNT(*) AS total FROM', $query);
        $countQuery = preg_replace('/ORDER BY.*$/i', '', $countQuery);
        
        $totalRecords = $db->getValue($countQuery, $params);
        
        // إضافة حدود الترقيم إلى الاستعلام الأصلي
        $query .= " LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
    }

    // تنفيذ الاستعلام
    $invoices = $db->getRows($query, $params);

    // حساب ملخص الفواتير
    $totalAmount = 0;
    $totalDiscount = 0;
    $paymentMethodCounts = [
        'cash' => 0,
        'card' => 0,
        'bank_transfer' => 0,
        'other' => 0
    ];
    $paymentStatusCounts = [
        'paid' => 0,
        'pending' => 0,
        'cancelled' => 0
    ];

    foreach ($invoices as $invoice) {
        $totalAmount += floatval($invoice['final_amount']);
        $totalDiscount += floatval($invoice['discount_amount']);
        
        // زيادة عداد طريقة الدفع
        if (isset($paymentMethodCounts[$invoice['payment_method']])) {
            $paymentMethodCounts[$invoice['payment_method']]++;
        }
        
        // زيادة عداد حالة الدفع
        if (isset($paymentStatusCounts[$invoice['payment_status']])) {
            $paymentStatusCounts[$invoice['payment_status']]++;
        }
    }

    // تحضير البيانات للاستجابة
    $summary = [
        'total_count' => count($invoices),
        'total_amount' => $totalAmount,
        'total_discount' => $totalDiscount,
        'payment_methods' => $paymentMethodCounts,
        'payment_statuses' => $paymentStatusCounts
    ];

    // بناء الاستجابة
    $responseData = [
        'invoices' => $invoices,
        'summary' => $summary
    ];

    // إضافة معلومات الترقيم إذا تم تطبيق الترقيم
    if (isset($totalRecords)) {
        $totalPages = ceil($totalRecords / $limit);
        $responseData['pagination'] = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'records_per_page' => $limit
        ];
    }

    // إضافة قوائم مرجعية للفلاتر
    $responseData['filters'] = [
        'payment_methods' => [
            ['id' => 'cash', 'name' => 'نقدي'],
            ['id' => 'card', 'name' => 'بطاقة'],
            ['id' => 'bank_transfer', 'name' => 'تحويل بنكي'],
            ['id' => 'other', 'name' => 'أخرى']
        ],
        'payment_statuses' => [
            ['id' => 'paid', 'name' => 'مدفوع'],
            ['id' => 'pending', 'name' => 'معلق'],
            ['id' => 'cancelled', 'name' => 'ملغي']
        ]
    ];

    // إضافة قائمة الكاشير للمدير ومدير الفرع
    if ($user_role === 'admin' || $user_role === 'manager') {
        $cashierQuery = "SELECT id, full_name FROM users WHERE role = 'cashier'";
        $cashierParams = [];
        
        if ($user_role === 'manager') {
            $cashierQuery .= " AND branch_id = ?";
            $cashierParams[] = $branch_id;
        }
        
        $cashiers = $db->getRows($cashierQuery, $cashierParams);
        $responseData['filters']['cashiers'] = $cashiers;
    }

    // إضافة قائمة الفروع للمدير
    if ($user_role === 'admin') {
        $branchesQuery = "SELECT id, name FROM branches WHERE active = 1";
        $branches = $db->getRows($branchesQuery);
        $responseData['filters']['branches'] = $branches;
    }

    // تعيين الاستجابة النهائية
    $response['status'] = true;
    $response['message'] = 'تم قراءة الفواتير بنجاح';
    $response['data'] = $responseData;
} catch (Exception $e) {
    $response['message'] = 'حدث خطأ أثناء قراءة الفواتير: ' . $e->getMessage();
    
    // تسجيل الخطأ
    logError('خطأ في قراءة الفواتير: ' . $e->getMessage());
}

// إرسال الاستجابة بتنسيق JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);