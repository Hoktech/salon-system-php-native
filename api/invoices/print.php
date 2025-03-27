<?php
// السماح بالوصول من أي مصدر (يمكن تعديله للأمان)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// تضمين ملفات التهيئة
include_once '../config/Database.php';
include_once '../config/Auth.php';

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

// التحقق من وجود معرف الفاتورة
if(!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'معرف الفاتورة مطلوب'
    ]);
    exit;
}

// الحصول على معرف الفاتورة
$invoice_id = $_GET['id'];

// الحصول على معلومات الفاتورة
$query = "SELECT i.*, c.full_name as customer_name, c.phone as customer_phone, b.name as branch_name, u.full_name as cashier_name
          FROM invoices i
          LEFT JOIN customers c ON i.customer_id = c.id
          LEFT JOIN branches b ON i.branch_id = b.id
          LEFT JOIN users u ON i.cashier_id = u.id
          WHERE i.id = ?";
          
$stmt = $db->prepare($query);
$stmt->execute([$invoice_id]);

if($stmt->rowCount() == 0) {
    http_response_code(404);
    echo json_encode([
        'status' => false,
        'message' => 'الفاتورة غير موجودة'
    ]);
    exit;
}

$invoice = $stmt->fetch();

// الحصول على تفاصيل الخدمات
$services_query = "SELECT is.*, s.name as service_name, u.full_name as employee_name
                  FROM invoice_services is
                  JOIN services s ON is.service_id = s.id
                  LEFT JOIN users u ON is.employee_id = u.id
                  WHERE is.invoice_id = ?";
                  
$services_stmt = $db->prepare($services_query);
$services_stmt->execute([$invoice_id]);
$services = $services_stmt->fetchAll();

// الحصول على تفاصيل المنتجات
$products_query = "SELECT ip.*, p.name as product_name
                  FROM invoice_products ip
                  JOIN products p ON ip.product_id = p.id
                  WHERE ip.invoice_id = ?";
                  
$products_stmt = $db->prepare($products_query);
$products_stmt->execute([$invoice_id]);
$products = $products_stmt->fetchAll();

// إنشاء محتوى الفاتورة للطباعة
$invoice_data = [
    'invoice_info' => [
        'invoice_number' => $invoice['invoice_number'],
        'invoice_date' => $invoice['invoice_date'],
        'branch_name' => $invoice['branch_name'],
        'cashier_name' => $invoice['cashier_name']
    ],
    'customer_info' => [
        'customer_name' => $invoice['customer_name'] ?? 'عميل عام',
        'customer_phone' => $invoice['customer_phone'] ?? ''
    ],
    'items' => [
        'services' => $services,
        'products' => $products
    ],
    'totals' => [
        'total_amount' => $invoice['total_amount'],
        'discount_amount' => $invoice['discount_amount'],
        'final_amount' => $invoice['final_amount'],
        'payment_method' => $invoice['payment_method']
    ]
];

// إرجاع البيانات
http_response_code(200);
echo json_encode([
    'status' => true,
    'message' => 'تم استرجاع بيانات الفاتورة بنجاح',
    'data' => $invoice_data,
    'print_html' => generatePrintHTML($invoice_data)
]);

// دالة إنشاء HTML للطباعة
function generatePrintHTML($invoice_data) {
    $html = '
    <!DOCTYPE html>
    <html dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>فاتورة #' . $invoice_data['invoice_info']['invoice_number'] . '</title>
        <style>
            @page {
                margin: 0;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 0;
                padding: 10px;
                direction: rtl;
                width: 80mm;
            }
            .header {
                text-align: center;
                margin-bottom: 10px;
            }
            .logo {
                max-width: 100%;
                height: auto;
            }
            .invoice-info {
                margin-bottom: 10px;
            }
            .customer-info {
                margin-bottom: 10px;
            }
            .items {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            .items th, .items td {
                border-bottom: 1px dashed #ccc;
                padding: 5px;
                text-align: right;
            }
            .items th {
                font-weight: bold;
            }
            .totals {
                width: 100%;
                margin-bottom: 10px;
            }
            .totals td {
                padding: 5px;
            }
            .footer {
                text-align: center;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>' . $invoice_data['invoice_info']['branch_name'] . '</h2>
            <p>فاتورة ضريبية مبسطة</p>
        </div>
        
        <div class="invoice-info">
            <p>رقم الفاتورة: ' . $invoice_data['invoice_info']['invoice_number'] . '</p>
            <p>تاريخ الفاتورة: ' . $invoice_data['invoice_info']['invoice_date'] . '</p>
            <p>الكاشير: ' . $invoice_data['invoice_info']['cashier_name'] . '</p>
        </div>
        
        <div class="customer-info">
            <p>العميل: ' . $invoice_data['customer_info']['customer_name'] . '</p>
            <p>الهاتف: ' . $invoice_data['customer_info']['customer_phone'] . '</p>
        </div>
        
        <table class="items">
            <tr>
                <th>البند</th>
                <th>السعر</th>
                <th>الكمية</th>
                <th>الخصم</th>
                <th>الإجمالي</th>
            </tr>';
            
    // إضافة الخدمات
    foreach($invoice_data['items']['services'] as $service) {
        $html .= '
            <tr>
                <td>' . $service['service_name'] . '</td>
                <td>' . number_format($service['price'], 2) . '</td>
                <td>' . $service['quantity'] . '</td>
                <td>' . number_format($service['discount'], 2) . '</td>
                <td>' . number_format($service['total'], 2) . '</td>
            </tr>';
    }
    
    // إضافة المنتجات
    foreach($invoice_data['items']['products'] as $product) {
        $html .= '
            <tr>
                <td>' . $product['product_name'] . '</td>
                <td>' . number_format($product['price'], 2) . '</td>
                <td>' . $product['quantity'] . '</td>
                <td>' . number_format($product['discount'], 2) . '</td>
                <td>' . number_format($product['total'], 2) . '</td>
            </tr>';
    }
    
    $html .= '
        </table>
        
        <table class="totals">
            <tr>
                <td>إجمالي:</td>
                <td>' . number_format($invoice_data['totals']['total_amount'], 2) . '</td>
            </tr>
            <tr>
                <td>الخصم:</td>
                <td>' . number_format($invoice_data['totals']['discount_amount'], 2) . '</td>
            </tr>
            <tr>
                <td>المبلغ النهائي:</td>
                <td>' . number_format($invoice_data['totals']['final_amount'], 2) . '</td>
            </tr>
            <tr>
                <td>طريقة الدفع:</td>
                <td>';
    
    // ترجمة طريقة الدفع
    switch($invoice_data['totals']['payment_method']) {
        case 'cash':
            $html .= 'نقداً';
            break;
        case 'card':
            $html .= 'بطاقة ائتمان';
            break;
        case 'bank_transfer':
            $html .= 'تحويل بنكي';
            break;
        default:
            $html .= 'أخرى';
    }
    
    $html .= '
                </td>
            </tr>
        </table>
        
        <div class="footer">
            <p>شكراً لزيارتكم</p>
            <p>نتمنى لكم يوماً سعيداً</p>
        </div>
    </body>
    </html>';
    
    return $html;
}
?>