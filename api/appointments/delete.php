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
        throw new Exception("معرّف الموعد مطلوب");
    }
    
    // Sanitize and validate input
    $id = (int)$data->id;
    
    // Check if appointment exists
    $stmt = $db->prepare("SELECT id, branch_id FROM appointments WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("الموعد غير موجود");
    }
    
    // Get the appointment branch
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if user has access to this appointment (admin can access all branches)
    if ($user['role'] !== 'admin' && $appointment['branch_id'] != $user['branch_id']) {
        throw new Exception("ليس لديك صلاحية للوصول إلى هذا الموعد");
    }
    
    // Delete the appointment
    $query = "DELETE FROM appointments WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    
    if ($stmt->execute()) {
        $response['status'] = true;
        $response['message'] = "تم حذف الموعد بنجاح";
    } else {
        throw new Exception("غير قادر على حذف الموعد");
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>