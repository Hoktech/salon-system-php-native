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
        throw new Exception("معرّف المستخدم مطلوب");
    }
    
    // Sanitize and validate input
    $id = (int)$data->id;
    
    // Check if trying to delete self
    if ($id === $user['id']) {
        throw new Exception("لا يمكنك حذف حسابك الخاص");
    }
    
    // Check if user exists
    $stmt = $db->prepare("SELECT id, username, branch_id, role FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("المستخدم غير موجود");
    }
    
    // Get the user info
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if current user has permission to delete this user
    if ($user['role'] !== 'admin') {
        // Managers can only delete users in their own branch
        if ($userInfo['branch_id'] != $user['branch_id']) {
            throw new Exception("ليس لديك صلاحية لحذف مستخدمين في فروع أخرى");
        }
        
        // Managers cannot delete admin users
        if ($userInfo['role'] === 'admin') {
            throw new Exception("ليس لديك صلاحية لحذف مستخدمين بدور مدير النظام");
        }
        
        // Managers cannot delete other managers
        if ($userInfo['role'] === 'manager') {
            throw new Exception("ليس لديك صلاحية لحذف مستخدمين بدور مدير");
        }
    }
    
    // Check if user is used in appointments or invoices
    $stmt = $db->prepare(
        "SELECT COUNT(*) as count FROM appointments WHERE employee_id = :id
         UNION ALL
         SELECT COUNT(*) as count FROM invoice_services WHERE employee_id = :id
         UNION ALL
         SELECT COUNT(*) as count FROM invoices WHERE cashier_id = :id"
    );
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    $counts = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $hasAppointments = $counts[0] > 0;
    $hasInvoiceServices = $counts[1] > 0;
    $hasInvoices = $counts[2] > 0;
    
    if ($hasAppointments || $hasInvoiceServices || $hasInvoices) {
        throw new Exception("لا يمكن حذف المستخدم لأنه مرتبط بمواعيد أو فواتير. يمكنك تعطيله بدلاً من ذلك");
    }
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Delete user permissions
        $stmt = $db->prepare("DELETE FROM user_permissions WHERE user_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Delete employee services
        $stmt = $db->prepare("DELETE FROM employee_services WHERE employee_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Delete the user
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Commit transaction
        $db->commit();
        
        $response['status'] = "success";
        $response['message'] = "تم حذف المستخدم بنجاح";
        $response['data'] = ["id" => $id, "username" => $userInfo['username']];
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw new Exception("حدث خطأ أثناء حذف المستخدم: " . $e->getMessage());
    }
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>