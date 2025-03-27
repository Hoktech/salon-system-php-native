<?php
/**
 * فئة الاتصال بقاعدة البيانات
 * تستخدم PDO للاتصال بقاعدة البيانات وتنفيذ الاستعلامات
 */
class Database {
    // معلومات الاتصال بقاعدة البيانات
    private $host = "localhost";
    private $db_name = "salon_system";
    private $username = "root";
    private $password = "";
    private $conn;
    
    /**
     * الحصول على اتصال بقاعدة البيانات
     * 
     * @return PDO كائن اتصال PDO
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            // محاولة إنشاء اتصال جديد
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", 
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            // سجّل الخطأ واظهر رسالة للمستخدم
            error_log("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
            throw new Exception("حدث خطأ في الاتصال بقاعدة البيانات");
        }
        
        return $this->conn;
    }
    
    /**
     * بدء معاملة في قاعدة البيانات
     * 
     * @return bool نتيجة بدء المعاملة
     */
    public function beginTransaction() {
        if (!$this->conn) {
            $this->getConnection();
        }
        return $this->conn->beginTransaction();
    }
    
    /**
     * تأكيد المعاملة في قاعدة البيانات
     * 
     * @return bool نتيجة تأكيد المعاملة
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * التراجع عن المعاملة في حالة حدوث خطأ
     * 
     * @return bool نتيجة التراجع عن المعاملة
     */
    public function rollback() {
        return $this->conn->rollBack();
    }
    
    /**
     * تنفيذ استعلام مع حماية من حقن SQL
     * 
     * @param string $query استعلام SQL
     * @param array $params معطيات الاستعلام
     * @return PDOStatement نتيجة تنفيذ الاستعلام
     */
    public function executeQuery($query, $params = []) {
        if (!$this->conn) {
            $this->getConnection();
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("خطأ في تنفيذ الاستعلام: " . $e->getMessage());
            throw new Exception("حدث خطأ أثناء تنفيذ العملية");
        }
    }
    
    /**
     * الحصول على معرف آخر سجل تم إدخاله
     * 
     * @return int معرف آخر سجل
     */
    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * إغلاق الاتصال بقاعدة البيانات
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>