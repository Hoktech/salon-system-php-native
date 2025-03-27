<?php
// السماح بالوصول من أي مصدر (يمكن تعديله للأمان)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// تضمين ملفات التهيئة
include_once '../config/Database.php';
include_once '../config/Auth.php';

// التأكد من أن الطلب هو POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => false,
        'message' => 'طريقة غير مسموح بها'
    ]);
    exit;
}

// الحصول على بيانات الاتصال بقاعدة البيانات
$database = new Database();
$db = $database->getConnection();

// إنشاء كائن المصادقة
$auth = new Auth($db);

// التحقق من المصادقة
if(!$auth->validateToken()) {
    http_response_code(401);
    echo json_encode([
        'status' => false,
        'message' => 'غير مصرح له'
    ]);
    exit;
}

// التحقق من الصلاحيات
if(!$auth->checkPermission('close_day')) {
    http_response_code(403);
    echo json_encode([
        'status' => false,
        'message' => 'ليس لديك صلاحية لإغلاق اليوم'
    ]);
    exit;
}

// الحصول على البيانات المرسلة
$data = json_decode(file_get_contents("php://input"));

// الحصول على معلومات المستخدم الحالي
$current_user = $auth->getCurrentUser();
$user_id = $current_user['id'];
$branch_id = $current_user['branch_id'];

try {
    // بدء المعاملة
    $db->beginTransaction();
    
    // التحقق مما إذا كان اليوم قد تم إغلاقه بالفعل
    $check_query = "SELECT id FROM day_end WHERE branch_id = ? AND date = CURDATE()";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$branch_id]);
    
    if($check_stmt->rowCount() > 0) {
        throw new Exception('تم إغلاق اليوم بالفعل');
    }
    
    // الحصول على إجماليات المبيعات حسب طريقة الدفع
    $sales_query = "SELECT 
                    SUM(CASE WHEN payment_method = 'cash' THEN final_amount ELSE 0 END) as cash_total,
                    SUM(CASE WHEN payment_method = 'card' THEN final_amount ELSE 0 END) as card_total,
                    SUM(CASE WHEN payment_method NOT IN ('cash', 'card') THEN final_amount ELSE 0 END) as other_total
                FROM invoices 
                WHERE branch_id = ? AND DATE(invoice_date) = CURDATE() AND day_end_closed = 0";
                
    $sales_stmt = $db->prepare($sales_query);
    $sales_stmt->execute([$branch_id]);
    $sales = $sales_stmt->fetch();
    
    // الحصول على إجمالي المصروفات
    $expenses_query = "SELECT SUM(amount) as expenses_total 
                      FROM expenses 
                      WHERE branch_id = ? AND date = CURDATE() AND day_end_closed = 0";
                      
    $expenses_stmt = $db->prepare($expenses_query);
    $expenses_stmt->execute([$branch_id]);
    $expenses = $expenses_stmt->fetch();
    
    // حساب الإجماليات
    $cash_total = $sales['cash_total'] ?? 0;
    $card_total = $sales['card_total'] ?? 0;
    $other_total = $sales['other_total'] ?? 0;
    $expenses_total = $expenses['expenses_total'] ?? 0;
    $net_total = $cash_total + $card_total + $other_total - $expenses_total;
    
    // إنشاء سجل إغلاق اليوم
    $day_end_query = "INSERT INTO day_end (branch_id, date, closed_by, cash_total, card_total, other_total, expenses_total, net_total, closed_at, notes) 
                     VALUES (?, CURDATE(), ?, ?, ?, ?, ?, ?, NOW(), ?)";
                     
    $day_end_stmt = $db->prepare($day_end_query);
    $day_end_stmt->execute([
        $branch_id,
        $user_id,
        $cash_total,
        $card_total,
        $other_total,
        $expenses_total,
        $net_total,
        $data->notes ?? null
    ]);
    
    // تحديث الفواتير بأنها تم إغلاقها
    $update_invoices_query = "UPDATE invoices SET day_end_closed = 1 
                             WHERE branch_id = ? AND DATE(invoice_date) = CURDATE() AND day_end_closed = 0";
                             
    $update_invoices_stmt = $db->prepare($update_invoices_query);
    $update_invoices_stmt->execute([$branch_id]);
    
    // تحديث المصروفات بأنها تم إغلاقها
    $update_expenses_query = "UPDATE expenses SET day_end_closed = 1 
                            WHERE branch_id = ? AND date = CURDATE() AND day_end_closed = 0";
                            
    $update_expenses_stmt = $db->prepare($update_expenses_query);
    $update_expenses_stmt->execute([$branch_id]);
    
    // تأكيد المعاملة
    $db->commit();
    
    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'تم إغلاق اليوم بنجاح',
        'data' => [
            'cash_total' => $cash_total,
            'card_total' => $card_total,
            'other_total' => $other_total,
            'expenses_total' => $expenses_total,
            'net_total' => $net_total,
            'closed_at' => date('Y-m-d H:i:s')
        ]
    ]);
} catch(Exception $e) {
    // التراجع عن المعاملة في حالة الخطأ
    $db->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'خطأ في إغلاق اليوم',
        'error' => $e->getMessage()
    ]);
}
?>