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
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (!isset($data->id) || !isset($data->full_name) || empty($data->full_name) || 
        !isset($data->phone) || empty($data->phone)) {
        throw new Exception("معرّف العميل، الاسم الكامل ورقم الهاتف مطلوبة");
    }
    
    // Sanitize and validate input
    $id = (int)$data->id;
    $fullName = sanitizeInput($data->full_name);
    $phone = sanitizeInput($data->phone);
    $email = isset($data->email) ? sanitizeInput($data->email) : null;
    $birthdate = isset($data->birthdate) && !empty($data->birthdate) ? $data->birthdate : null;
    $gender = isset($data->gender) ? sanitizeInput($data->gender) : 'male';
    $address = isset($data->address) ? sanitizeInput($data->address) : null;
    $notes = isset($data->notes) ? sanitizeInput($data->notes) : null;
    $branch_id = isset($data->branch_id) ? (int)$data->branch_id : $user['branch_id'];
    
    // Check if customer exists
    $stmt = $db->prepare("SELECT id, branch_id FROM customers WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("العميل غير موجود");
    }
    
    // Get the customer branch
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if user has access to this customer (admin can access all branches)
    if ($user['role'] !== 'admin' && $customer['branch_id'] != $user['branch_id']) {
        throw new Exception("ليس لديك صلاحية للوصول إلى هذا العميل");
    }
    
    // Check for duplicate phone number (excluding the current customer)
    $stmt = $db->prepare("SELECT id FROM customers WHERE phone = :phone AND id != :id");
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception("رقم الهاتف مستخدم بالفعل لعميل آخر");
    }
    
    // Update the customer
    $query = "
        UPDATE customers SET
            full_name = :full_name,
            phone = :phone,
            email = :email,
            birthdate = :birthdate,
            gender = :gender,
            address = :address,
            notes = :notes,
            branch_id = :branch_id,
            updated_at = NOW()
        WHERE id = :id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":full_name", $fullName);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":birthdate", $birthdate);
    $stmt->bindParam(":gender", $gender);
    $stmt->bindParam(":address", $address);
    $stmt->bindParam(":notes", $notes);
    $stmt->bindParam(":branch_id", $branch_id);
    $stmt->bindParam(":id", $id);
    
    if ($stmt->execute()) {
        // Get the updated customer details for response
        $query = "SELECT * FROM customers WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $customerData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response['status'] = true;
        $response['message'] = "تم تحديث بيانات العميل بنجاح";
        $response['data'] = $customerData;
    } else {
        throw new Exception("غير قادر على تحديث بيانات العميل");
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>