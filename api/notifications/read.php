<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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
    "data" => []
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("طريقة طلب غير صالحة");
    }
    
    // Check user permissions
    $user = checkUserPermission(); // يمكن لأي مستخدم مسجل دخول الحصول على الإشعارات
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // تحديد عدد الإشعارات المطلوبة
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    // التحقق من وجود جدول notifications
    try {
        $checkTableQuery = "SHOW TABLES LIKE 'notifications'";
        $checkTableStmt = $db->prepare($checkTableQuery);
        $checkTableStmt->execute();
        
        if ($checkTableStmt->rowCount() === 0) {
            // الجدول غير موجود، نعيد مصفوفة فارغة
            $response['status'] = true;
            $response['message'] = "لا توجد إشعارات";
            $response['data'] = [];
            echo json_encode($response);
            exit;
        }
    } catch (Exception $e) {
        // في حالة حدوث خطأ، نعيد مصفوفة فارغة
        $response['status'] = true;
        $response['message'] = "لا توجد إشعارات";
        $response['data'] = [];
        echo json_encode($response);
        exit;
    }
    
    // استعلام الإشعارات
    $query = "
        SELECT n.id, n.user_id, n.type, n.message, n.related_id, n.status, n.created_at
        FROM notifications n
        WHERE n.user_id = :user_id OR n.user_id = 0
        ORDER BY n.created_at DESC
        LIMIT :limit
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user['id'], PDO::PARAM_INT);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    // تهيئة مصفوفة الإشعارات
    $notifications = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notification = [
            "id" => $row['id'],
            "type" => $row['type'],
            "message" => $row['message'],
            "related_id" => $row['related_id'],
            "status" => $row['status'],
            "date" => formatDate($row['created_at'], 'Y-m-d H:i:s', 'd/m/Y H:i')
        ];
        
        $notifications[] = $notification;
    }
    
    // تحديث حالة الإشعارات إلى "قرأت"
    if (!empty($notifications)) {
        $updateQuery = "
            UPDATE notifications 
            SET status = 'read' 
            WHERE (user_id = :user_id OR user_id = 0) AND status = 'new'
        ";
        
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(":user_id", $user['id'], PDO::PARAM_INT);
        $updateStmt->execute();
    }
    
    // إعداد الاستجابة
    $response['status'] = true;
    $response['message'] = count($notifications) > 0 ? "تم جلب الإشعارات بنجاح" : "لا توجد إشعارات";
    $response['data'] = $notifications;
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>