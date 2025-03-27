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
    "status" => "success",
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
        throw new Exception("معرّف الخدمة مطلوب");
    }
    
    // Sanitize and validate input
    $id = (int)$data->id;
    
    // Check if service exists
    $stmt = $db->prepare("SELECT id, branch_id, name FROM services WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("الخدمة غير موجودة");
    }
    
    // Get service data
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if user has access to this service (admin can access all branches)
    if ($user['role'] !== 'admin' && $service['branch_id'] != $user['branch_id']) {
        throw new Exception("ليس لديك صلاحية للوصول إلى هذه الخدمة");
    }
    
    // Check if service is used in invoices or appointments
    $stmt = $db->prepare(
        "SELECT COUNT(*) as count FROM invoice_services WHERE service_id = :id
         UNION ALL
         SELECT COUNT(*) as count FROM appointments WHERE service_id = :id"
    );
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    $counts = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $hasInvoices = $counts[0] > 0;
    $hasAppointments = $counts[1] > 0;
    
    if ($hasInvoices || $hasAppointments) {
        throw new Exception("لا يمكن حذف الخدمة لأنها مستخدمة في الفواتير أو المواعيد. يمكنك تعطيلها بدلاً من ذلك");
    }
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Delete employee assignments
        $stmt = $db->prepare("DELETE FROM employee_services WHERE service_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Delete the service
        $stmt = $db->prepare("DELETE FROM services WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Commit transaction
        $db->commit();
        
        $response['status'] = "success";
        $response['message'] = "تم حذف الخدمة بنجاح";
        $response['data'] = ["id" => $id, "name" => $service['name']];
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw new Exception("حدث خطأ أثناء حذف الخدمة: " . $e->getMessage());
    }
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>