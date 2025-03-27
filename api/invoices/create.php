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
if(!$auth->checkPermission('create_invoices')) {
    http_response_code(403);
    echo json_encode([
        'status' => false,
        'message' => 'ليس لديك صلاحية لإنشاء الفواتير'
    ]);
    exit;
}

// الحصول على البيانات المرسلة
$data = json_decode(file_get_contents("php://input"));

// التأكد من وجود البيانات المطلوبة
if(!isset($data->customer_id) || 
   !isset($data->total_amount) || 
   !isset($data->final_amount) || 
   !isset($data->payment_method) ||
   !isset($data->services) && !isset($data->products)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'بيانات غير مكتملة'
    ]);
    exit;
}

// الحصول على معلومات المستخدم الحالي
$current_user = $auth->getCurrentUser();
$cashier_id = $current_user['id'];
$branch_id = $current_user['branch_id'];

try {
    // بدء المعاملة
    $db->beginTransaction();
    
    // إنشاء رقم الفاتورة
    $invoice_number = date('Ymd') . sprintf('%04d', mt_rand(1, 9999));
    
    // إدراج الفاتورة
    $query = "INSERT INTO invoices (invoice_number, customer_id, cashier_id, total_amount, discount_amount, final_amount, payment_method, payment_status, notes, branch_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
              
    $stmt = $db->prepare($query);
    $stmt->execute([
        $invoice_number,
        $data->customer_id,
        $cashier_id,
        $data->total_amount,
        $data->discount_amount ?? 0,
        $data->final_amount,
        $data->payment_method,
        $data->payment_status ?? 'paid',
        $data->notes ?? null,
        $branch_id
    ]);
    
    // الحصول على معرف الفاتورة المضافة
    $invoice_id = $db->lastInsertId();
    
    // إضافة الخدمات إلى الفاتورة
    if(isset($data->services) && is_array($data->services)) {
        $service_query = "INSERT INTO invoice_services (invoice_id, service_id, employee_id, price, quantity, discount, total) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $service_stmt = $db->prepare($service_query);
        
        foreach($data->services as $service) {
            $service_stmt->execute([
                $invoice_id,
                $service->service_id,
                $service->employee_id ?? null,
                $service->price,
                $service->quantity ?? 1,
                $service->discount ?? 0,
                $service->total
            ]);
        }
    }
    
    // إضافة المنتجات إلى الفاتورة
    if(isset($data->products) && is_array($data->products)) {
        $product_query = "INSERT INTO invoice_products (invoice_id, product_id, price, quantity, discount, total) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        $product_stmt = $db->prepare($product_query);
        
        $update_stock_query = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
        $update_stock_stmt = $db->prepare($update_stock_query);
        
        foreach($data->products as $product) {
            $product_stmt->execute([
                $invoice_id,
                $product->product_id,
                $product->price,
                $product->quantity,
                $product->discount ?? 0,
                $product->total
            ]);
            
            // تحديث المخزون
            $update_stock_stmt->execute([
                $product->quantity,
                $product->product_id
            ]);
            
            // التحقق من المخزون المنخفض
            $check_stock_query = "SELECT id, stock_quantity, minimum_quantity FROM products WHERE id = ?";
            $check_stock_stmt = $db->prepare($check_stock_query);
            $check_stock_stmt->execute([$product->product_id]);
            $product_info = $check_stock_stmt->fetch();
            
            if($product_info['stock_quantity'] <= $product_info['minimum_quantity']) {
                $alert_type = $product_info['stock_quantity'] <= 0 ? 'out_of_stock' : 'low_stock';
                
                // إنشاء تنبيه المخزون
                $alert_query = "INSERT INTO inventory_alerts (product_id, alert_type) VALUES (?, ?)";
                $alert_stmt = $db->prepare($alert_query);
                $alert_stmt->execute([$product->product_id, $alert_type]);
            }
        }
    }
    
    // إضافة نقاط الولاء للعميل (إذا كان مطلوباً)
    if(isset($data->loyalty_points) && $data->loyalty_points > 0) {
        $loyalty_query = "INSERT INTO loyalty_transactions (customer_id, points, transaction_type, invoice_id, notes) 
                         VALUES (?, ?, 'earn', ?, 'نقاط مكتسبة من الفاتورة')";
        $loyalty_stmt = $db->prepare($loyalty_query);
        $loyalty_stmt->execute([
            $data->customer_id,
            $data->loyalty_points,
            $invoice_id
        ]);
        
        // تحديث نقاط العميل
        $update_points_query = "UPDATE customers SET loyalty_points = loyalty_points + ? WHERE id = ?";
        $update_points_stmt = $db->prepare($update_points_query);
        $update_points_stmt->execute([
            $data->loyalty_points,
            $data->customer_id
        ]);
    }
    
    // تأكيد المعاملة
    $db->commit();
    
    http_response_code(201);
    echo json_encode([
        'status' => true,
        'message' => 'تم إنشاء الفاتورة بنجاح',
        'invoice_id' => $invoice_id,
        'invoice_number' => $invoice_number
    ]);
} catch(PDOException $e) {
    // التراجع عن المعاملة في حالة الخطأ
    $db->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'خطأ في إنشاء الفاتورة',
        'error' => $e->getMessage()
    ]);
}
?>