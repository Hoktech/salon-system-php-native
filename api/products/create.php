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
    if (!isset($data->name) || empty($data->name) || !isset($data->selling_price)) {
        throw new Exception("اسم المنتج والسعر مطلوبان");
    }
    
    // Sanitize and validate input
    $name = sanitizeInput($data->name);
    $category = isset($data->category) ? sanitizeInput($data->category) : null;
    $purchase_price = isset($data->purchase_price) ? (float)$data->purchase_price : null;
    $selling_price = (float)$data->selling_price;
    $stock_quantity = isset($data->stock_quantity) ? (int)$data->stock_quantity : 0;
    $minimum_quantity = isset($data->minimum_quantity) ? (int)$data->minimum_quantity : 5;
    $for_sale = isset($data->for_sale) ? (int)$data->for_sale : 1;
    $for_internal_use = isset($data->for_internal_use) ? (int)$data->for_internal_use : 0;
    $branch_id = isset($data->branch_id) ? (int)$data->branch_id : $user['branch_id'];
    
    // Check if product with same name already exists in the branch
    $stmt = $db->prepare("SELECT id FROM products WHERE name = :name AND branch_id = :branch_id");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":branch_id", $branch_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception("يوجد منتج بنفس الاسم في هذا الفرع");
    }
    
    // Insert the product
    $query = "
        INSERT INTO products (
            name, category, purchase_price, selling_price, stock_quantity, 
            minimum_quantity, for_sale, for_internal_use, branch_id, created_at
        ) VALUES (
            :name, :category, :purchase_price, :selling_price, :stock_quantity, 
            :minimum_quantity, :for_sale, :for_internal_use, :branch_id, NOW()
        )
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
    
    if ($stmt->execute()) {
        $product_id = $db->lastInsertId();
        
        // Get the product details for response
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if stock is below minimum and create alert
        if ($stock_quantity <= $minimum_quantity) {
            $alert_type = ($stock_quantity <= 0) ? 'out_of_stock' : 'low_stock';
            
            $alert_query = "
                INSERT INTO inventory_alerts (product_id, alert_type, status, created_at)
                VALUES (:product_id, :alert_type, 'new', NOW())
            ";
            
            $alert_stmt = $db->prepare($alert_query);
            $alert_stmt->bindParam(":product_id", $product_id);
            $alert_stmt->bindParam(":alert_type", $alert_type);
            $alert_stmt->execute();
        }
        
        $response['status'] = true;
        $response['message'] = "تم إضافة المنتج بنجاح";
        $response['data'] = $product;
    } else {
        throw new Exception("حدث خطأ أثناء إضافة المنتج");
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);