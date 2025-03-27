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
    if (!isset($data->id) || !isset($data->name) || empty($data->name) || !isset($data->selling_price)) {
        throw new Exception("معرّف المنتج، الاسم والسعر مطلوبان");
    }
    
    // Sanitize and validate input
    $id = (int)$data->id;
    $name = sanitizeInput($data->name);
    $category = isset($data->category) ? sanitizeInput($data->category) : null;
    $purchase_price = isset($data->purchase_price) ? (float)$data->purchase_price : null;
    $selling_price = (float)$data->selling_price;
    $stock_quantity = isset($data->stock_quantity) ? (int)$data->stock_quantity : 0;
    $minimum_quantity = isset($data->minimum_quantity) ? (int)$data->minimum_quantity : 5;
    $for_sale = isset($data->for_sale) ? (int)$data->for_sale : 1;
    $for_internal_use = isset($data->for_internal_use) ? (int)$data->for_internal_use : 0;
    $branch_id = isset($data->branch_id) ? (int)$data->branch_id : $user['branch_id'];
    
    // Check if product exists
    $stmt = $db->prepare("SELECT id, branch_id, stock_quantity FROM products WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("المنتج غير موجود");
    }
    
    // Get the product's branch and current stock
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $previous_stock = $product['stock_quantity'];
    
    // Check if user has access to this product (admin can access all branches)
    if ($user['role'] !== 'admin' && $product['branch_id'] != $user['branch_id']) {
        throw new Exception("ليس لديك صلاحية للوصول إلى هذا المنتج");
    }
    
    // Check if product with same name already exists in the branch (excluding current product)
    $stmt = $db->prepare("SELECT id FROM products WHERE name = :name AND branch_id = :branch_id AND id != :id");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":branch_id", $branch_id);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception("يوجد منتج آخر بنفس الاسم في هذا الفرع");
    }
    
    // Update the product
    $query = "
        UPDATE products SET
            name = :name,
            category = :category,
            purchase_price = :purchase_price,
            selling_price = :selling_price,
            stock_quantity = :stock_quantity,
            minimum_quantity = :minimum_quantity,
            for_sale = :for_sale,
            for_internal_use = :for_internal_use,
            branch_id = :branch_id,
            updated_at = NOW()
        WHERE id = :id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":purchase_price", $purchase_price);
    $stmt->bindParam(":selling_price", $selling_price);
    $stmt->bindParam(":stock_quantity", $stock_quantity);
    $stmt->bindParam(":minimum_quantity", $minimum_quantity);
    $stmt->bindParam(":for_sale", $for_sale);
    $stmt->bindParam(":for_internal_use", $for_internal_use);
    $stmt->bindParam(":branch_id", $branch_id);
    $stmt->bindParam(":id", $id);
    
    if ($stmt->execute()) {
        // Check if stock level has changed and if it's below minimum
        if ($stock_quantity <= $minimum_quantity) {
            $alert_type = ($stock_quantity <= 0) ? 'out_of_stock' : 'low_stock';
            
            // Check if alert already exists
            $stmt = $db->prepare("SELECT id FROM inventory_alerts WHERE product_id = :product_id AND status != 'resolved'");
            $stmt->bindParam(":product_id", $id);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                // Create new alert
                $alert_query = "
                    INSERT INTO inventory_alerts (product_id, alert_type, status, created_at)
                    VALUES (:product_id, :alert_type, 'new', NOW())
                ";
                
                $alert_stmt = $db->prepare($alert_query);
                $alert_stmt->bindParam(":product_id", $id);
                $alert_stmt->bindParam(":alert_type", $alert_type);
                $alert_stmt->execute();
            } else {
                // Update existing alert
                $alert_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                $alert_query = "
                    UPDATE inventory_alerts 
                    SET alert_type = :alert_type, status = 'new', updated_at = NOW()
                    WHERE id = :id
                ";
                
                $alert_stmt = $db->prepare($alert_query);
                $alert_stmt->bindParam(":alert_type", $alert_type);
                $alert_stmt->bindParam(":id", $alert_id);
                $alert_stmt->execute();
            }
        } else if ($previous_stock <= $minimum_quantity && $stock_quantity > $minimum_quantity) {
            // Stock level has increased above minimum - resolve any existing alerts
            $stmt = $db->prepare("
                UPDATE inventory_alerts 
                SET status = 'resolved', updated_at = NOW()
                WHERE product_id = :product_id AND status != 'resolved'
            ");
            $stmt->bindParam(":product_id", $id);
            $stmt->execute();
        }
        
        // Get the updated product details
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response['status'] = true;
        $response['message'] = "تم تحديث المنتج بنجاح";
        $response['data'] = $product;
    } else {
        throw new Exception("حدث خطأ أثناء تحديث المنتج");
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>