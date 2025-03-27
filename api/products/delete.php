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
    $user = checkUserPermission(['admin', 'manager']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (!isset($data->id)) {
        throw new Exception("معرّف المنتج مطلوب");
    }
    
    // Sanitize and validate input
    $id = (int)$data->id;
    
    // Check if product exists
    $stmt = $db->prepare("SELECT id, branch_id, name FROM products WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("المنتج غير موجود");
    }
    
    // Get product data
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if user has access to this product (admin can access all branches)
    if ($user['role'] !== 'admin' && $product['branch_id'] != $user['branch_id']) {
        throw new Exception("ليس لديك صلاحية للوصول إلى هذا المنتج");
    }
    
    // Check if product is used in invoices
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM invoice_products WHERE product_id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        throw new Exception("لا يمكن حذف المنتج لأنه مستخدم في الفواتير. يمكنك تعطيله بدلاً من ذلك");
    }
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Delete any inventory alerts for this product
        $stmt = $db->prepare("DELETE FROM inventory_alerts WHERE product_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Delete the product
        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Commit transaction
        $db->commit();
        
        $response['status'] = true;
        $response['message'] = "تم حذف المنتج بنجاح";
        $response['data'] = ["id" => $id, "name" => $product['name']];
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw new Exception("حدث خطأ أثناء حذف المنتج: " . $e->getMessage());
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>