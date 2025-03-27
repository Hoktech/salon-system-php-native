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
    if (!isset($data->id) || !isset($data->name) || empty($data->name) || !isset($data->price) || !is_numeric($data->price)) {
        throw new Exception("معرّف الخدمة، الاسم والسعر مطلوبان");
    }
    
    // Sanitize and validate input
    $id = (int)$data->id;
    $name = sanitizeInput($data->name);
    $category = isset($data->category) ? sanitizeInput($data->category) : null;
    $price = (float)$data->price;
    $duration = isset($data->duration) && is_numeric($data->duration) ? (int)$data->duration : 30;
    $description = isset($data->description) ? sanitizeInput($data->description) : null;
    $active = isset($data->active) ? (int)$data->active : 1;
    $branch_id = isset($data->branch_id) ? (int)$data->branch_id : $user['branch_id'];
    
    // Check if service exists
    $stmt = $db->prepare("SELECT id, branch_id FROM services WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("الخدمة غير موجودة");
    }
    
    // Get the service's branch
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if user has access to this service (admin can access all branches)
    if ($user['role'] !== 'admin' && $service['branch_id'] != $user['branch_id']) {
        throw new Exception("ليس لديك صلاحية للوصول إلى هذه الخدمة");
    }
    
    // Check if service with same name already exists in the branch (excluding current service)
    $stmt = $db->prepare("SELECT id FROM services WHERE name = :name AND branch_id = :branch_id AND id != :id");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":branch_id", $branch_id);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception("يوجد خدمة أخرى بنفس الاسم في هذا الفرع");
    }
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Update the service
        $query = "
            UPDATE services SET
                name = :name,
                category = :category,
                price = :price,
                duration = :duration,
                description = :description,
                active = :active,
                branch_id = :branch_id,
                updated_at = NOW()
            WHERE id = :id
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":duration", $duration);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":active", $active);
        $stmt->bindParam(":branch_id", $branch_id);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Update employee assignments if provided
        if (isset($data->employees) && is_array($data->employees)) {
            // First, remove all existing assignments
            $stmt = $db->prepare("DELETE FROM employee_services WHERE service_id = :service_id");
            $stmt->bindParam(":service_id", $id);
            $stmt->execute();
            
            // Then, add new assignments
            if (!empty($data->employees)) {
                $query = "INSERT INTO employee_services (employee_id, service_id) VALUES (:employee_id, :service_id)";
                $stmt = $db->prepare($query);
                
                foreach ($data->employees as $employeeId) {
                    // Validate employee
                    $checkQuery = "SELECT id FROM users WHERE id = :id AND role = 'employee' AND active = 1";
                    $checkStmt = $db->prepare($checkQuery);
                    $checkStmt->bindParam(":id", $employeeId);
                    $checkStmt->execute();
                    
                    if ($checkStmt->rowCount() > 0) {
                        $stmt->bindParam(":employee_id", $employeeId);
                        $stmt->bindParam(":service_id", $id);
                        $stmt->execute();
                    }
                }
            }
        }
        
        // Commit transaction
        $db->commit();
        
        // Get the updated service details
        $query = "SELECT * FROM services WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response['status'] = "success";
        $response['message'] = "تم تحديث الخدمة بنجاح";
        $response['data'] = $service;
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw new Exception("حدث خطأ أثناء تحديث الخدمة: " . $e->getMessage());
    }
    
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>