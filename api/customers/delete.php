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
        throw new Exception("معرّف العميل مطلوب");
    }
    
    // Sanitize and validate input
    $id = (int)$data->id;
    
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
    
    // Check if customer has existing appointments or invoices
    $stmt = $db->prepare(
        "SELECT COUNT(*) as count FROM appointments WHERE customer_id = :id
         UNION ALL
         SELECT COUNT(*) as count FROM invoices WHERE customer_id = :id"
    );
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    $counts = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $hasAppointments = $counts[0] > 0;
    $hasInvoices = $counts[1] > 0;
    
    if ($hasAppointments || $hasInvoices) {
        // Begin transaction
        $db->beginTransaction();
        
        try {
            // Update customer_id to NULL in appointments
            if ($hasAppointments) {
                $stmt = $db->prepare("UPDATE appointments SET customer_id = NULL WHERE customer_id = :id");
                $stmt->bindParam(":id", $id);
                $stmt->execute();
            }
            
            // Update customer_id to NULL in invoices
            if ($hasInvoices) {
                $stmt = $db->prepare("UPDATE invoices SET customer_id = NULL WHERE customer_id = :id");
                $stmt->bindParam(":id", $id);
                $stmt->execute();
            }
            
            // Delete loyalty transactions
            $stmt = $db->prepare("DELETE FROM loyalty_transactions WHERE customer_id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            // Delete the customer
            $stmt = $db->prepare("DELETE FROM customers WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            // Commit transaction
            $db->commit();
            
            $response['status'] = true;
            $response['message'] = "تم حذف العميل بنجاح وتحديث السجلات المرتبطة";
        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();
            throw new Exception("حدث خطأ أثناء حذف العميل: " . $e->getMessage());
        }
    } else {
        // No related records, directly delete the customer
        $stmt = $db->prepare("DELETE FROM customers WHERE id = :id");
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            $response['status'] = true;
            $response['message'] = "تم حذف العميل بنجاح";
        } else {
            throw new Exception("غير قادر على حذف العميل");
        }
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>