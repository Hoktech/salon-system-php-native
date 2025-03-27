<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and permission files
include_once '../config/database.php';
include_once '../config/helpers.php';
include_once '../auth/permissions.php';

// Initialize response array
$response = [
    "status" => false,
    "message" => "",
    "data" => null
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("طريقة طلب غير صالحة");
    }
    
    // Check user permissions
    $user = checkUserPermission(['admin', 'manager', 'cashier']);
    
    // Check specific permission if it's defined in the user's session
    if (isset($_SESSION['permissions']) && !empty($_SESSION['permissions'])) {
        if ($_SESSION['role'] !== 'admin' && !in_array('customers_add', $_SESSION['permissions'])) {
            throw new Exception("ليس لديك صلاحية لإضافة عملاء جدد");
        }
    }
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (
        !isset($data->full_name) || 
        !isset($data->phone)
    ) {
        throw new Exception("يرجى تعبئة جميع الحقول المطلوبة");
    }
    
    // Sanitize and validate input
    $full_name = sanitizeInput($data->full_name);
    $phone = sanitizeInput($data->phone);
    $email = isset($data->email) ? sanitizeInput($data->email) : null;
    $birthdate = isset($data->birthdate) ? sanitizeInput($data->birthdate) : null;
    $address = isset($data->address) ? sanitizeInput($data->address) : null;
    $notes = isset($data->notes) ? sanitizeInput($data->notes) : null;
    $gender = isset($data->gender) ? sanitizeInput($data->gender) : "male";
    $branch_id = isset($data->branch_id) ? (int)$data->branch_id : $user['branch_id'];
    
    // Validate name
    if (empty($full_name)) {
        throw new Exception("يرجى إدخال اسم العميل");
    }
    
    // Validate phone number
    if (empty($phone)) {
        throw new Exception("يرجى إدخال رقم هاتف العميل");
    }
    
    // Check if phone number already exists
    $stmt = $db->prepare("SELECT id FROM customers WHERE phone = :phone");
    $stmt->bindParam(":phone", $phone);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception("رقم الهاتف مسجل بالفعل لعميل آخر");
    }
    
    // Validate email if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("البريد الإلكتروني غير صالح");
    }
    
    // Validate birthdate if provided
    if (!empty($birthdate) && !validateDate($birthdate, 'Y-m-d')) {
        throw new Exception("صيغة تاريخ الميلاد غير صحيحة. استخدم التنسيق YYYY-MM-DD");
    }
    
    // Validate gender
    if (!in_array($gender, ['male', 'female'])) {
        throw new Exception("الجنس غير صالح");
    }
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Insert customer
        $query = "
            INSERT INTO customers (
                full_name, phone, email, birthdate, address, gender, 
                notes, branch_id, created_at
            ) VALUES (
                :full_name, :phone, :email, :birthdate, :address, :gender, 
                :notes, :branch_id, NOW()
            )
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":full_name", $full_name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":birthdate", $birthdate);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":notes", $notes);
        $stmt->bindParam(":branch_id", $branch_id);
        
        if (!$stmt->execute()) {
            throw new Exception("حدث خطأ أثناء إضافة العميل");
        }
        
        // Get new customer ID
        $customer_id = $db->lastInsertId();
        
        // Log the activity
        logActivity(
            $user['id'], 
            'create', 
            "تمت إضافة عميل جديد: {$full_name}", 
            'customers'
        );
        
        // Commit the transaction
        $db->commit();
        
        // Prepare response data
        $customerData = [
            "id" => $customer_id,
            "full_name" => $full_name,
            "phone" => $phone,
            "email" => $email,
            "birthdate" => $birthdate,
            "address" => $address,
            "gender" => $gender,
            "notes" => $notes,
            "branch_id" => $branch_id,
            "loyalty_points" => 0,
            "created_at" => date('Y-m-d H:i:s')
        ];
        
        // Set successful response
        $response['status'] = true;
        $response['message'] = "تمت إضافة العميل بنجاح";
        $response['data'] = $customerData;
        
    } catch (Exception $e) {
        // Rollback on error
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>