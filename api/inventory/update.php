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
    "status" => true,
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
    if (!isset($data->product_id) || !isset($data->operation) || !isset($data->quantity)) {
        throw new Exception("معرّف المنتج، العملية والكمية مطلوبة");
    }
    
    // Sanitize and validate input
    $product_id = (int)$data->product_id;
    $operation = sanitizeInput($data->operation);
    $quantity = (int)$data->quantity;
    $minimum_quantity = isset($data->minimum_quantity) ? (int)$data->minimum_quantity : null;
    $notes = isset($data->notes) ? sanitizeInput($data->notes) : null;
    
    // Validate operation
    if (!in_array($operation, ['add', 'subtract', 'set'])) {
        throw new Exception("عملية غير صالحة. الخيارات المتاحة: add, subtract, set");
    }
    
    // Validate quantity
    if ($quantity <= 0) {
        throw new Exception("يجب أن تكون الكمية أكبر من صفر");
    }
    
    // Check if product exists
    $stmt = $db->prepare("SELECT p.id, p.name, p.stock_quantity, p.minimum_quantity, p.branch_id FROM products p WHERE p.id = :id");
    $stmt->bindParam(":id", $product_id);
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
    
    // Calculate new stock quantity
    $new_stock = $product['stock_quantity'];
    
    switch ($operation) {
        case 'add':
            $new_stock += $quantity;
            break;
        case 'subtract':
            $new_stock = max(0, $new_stock - $quantity);
            break;
        case 'set':
            $new_stock = $quantity;
            break;
    }
    
    // Set minimum quantity if provided
    $new_minimum = $minimum_quantity !== null ? $minimum_quantity : $product['minimum_quantity'];
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Update product stock
        $query = "
            UPDATE products 
            SET stock_quantity = :stock_quantity, minimum_quantity = :minimum_quantity, updated_at = NOW()
            WHERE id = :id
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":stock_quantity", $new_stock);
        $stmt->bindParam(":minimum_quantity", $new_minimum);
        $stmt->bindParam(":id", $product_id);
        $stmt->execute();
        
        // Record the inventory transaction
        $transaction_query = "
            INSERT INTO inventory_transactions (
                product_id, transaction_type, quantity, previous_stock, new_stock, 
                user_id, notes, branch_id, created_at
            ) VALUES (
                :product_id, :transaction_type, :quantity, :previous_stock, :new_stock,
                :user_id, :notes, :branch_id, NOW()
            )
        ";
        
        $stmt = $db->prepare($transaction_query);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":transaction_type", $operation);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":previous_stock", $product['stock_quantity']);
        $stmt->bindParam(":new_stock", $new_stock);
        $stmt->bindParam(":user_id", $user['id']);
        $stmt->bindParam(":notes", $notes);
        $stmt->bindParam(":branch_id", $product['branch_id']);
        $stmt->execute();
        
        // Handle inventory alerts
        if ($new_stock <= $new_minimum) {
            $alert_type = ($new_stock <= 0) ? 'out_of_stock' : 'low_stock';
            
            // Check if alert already exists
            $alert_check_query = "
                SELECT id FROM inventory_alerts 
                WHERE product_id = :product_id AND status != 'resolved'
            ";
            
            $stmt = $db->prepare($alert_check_query);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                // Create new alert
                $alert_query = "
                    INSERT INTO inventory_alerts (product_id, alert_type, status, created_at)
                    VALUES (:product_id, :alert_type, 'new', NOW())
                ";
                
                $stmt = $db->prepare($alert_query);
                $stmt->bindParam(":product_id", $product_id);
                $stmt->bindParam(":alert_type", $alert_type);
                $stmt->execute();
            } else {
                // Update existing alert
                $alert = $stmt->fetch(PDO::FETCH_ASSOC);
                $alert_query = "
                    UPDATE inventory_alerts 
                    SET alert_type = :alert_type, status = 'new', updated_at = NOW()
                    WHERE id = :id
                ";
                
                $stmt = $db->prepare($alert_query);
                $stmt->bindParam(":alert_type", $alert_type);
                $stmt->bindParam(":id", $alert['id']);
                $stmt->execute();
            }
        } else if ($product['stock_quantity'] <= $product['minimum_quantity'] && $new_stock > $new_minimum) {
            // Resolve any existing alerts
            $alert_query = "
                UPDATE inventory_alerts 
                SET status = 'resolved', updated_at = NOW()
                WHERE product_id = :product_id AND status != 'resolved'
            ";
            
            $stmt = $db->prepare($alert_query);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->execute();
        }
        
        // Commit transaction
        $db->commit();
        
        // Get updated product data
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(":id", $product_id);
        $stmt->execute();
        $updated_product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Prepare response data
        $responseData = [
            'product' => $updated_product,
            'operation' => $operation,
            'quantity' => $quantity,
            'previous_stock' => $product['stock_quantity'],
            'new_stock' => $new_stock
        ];
        
        $response['status'] = true;
        $response['message'] = "تم تحديث المخزون بنجاح";
        $response['data'] = $responseData;
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw new Exception("حدث خطأ أثناء تحديث المخزون: " . $e->getMessage());
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>