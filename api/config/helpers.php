<?php
/**
 * ملف الدوال المساعدة للمشروع
 * يحتوي على دوال عامة تستخدم في مختلف أجزاء النظام
 */

/**
 * تنظيف المدخلات للحماية من هجمات XSS
 * 
 * @param mixed $data البيانات المراد تنظيفها
 * @return mixed البيانات بعد التنظيف
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeInput($value);
        }
        return $data;
    }
    
    // تنظيف النص من الرموز الخاصة HTML
    $data = htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * التحقق من صلاحيات المستخدم للوصول إلى الصفحة أو API
 * 
 * @param array $allowedRoles الأدوار المسموح لها بالوصول
 * @return array بيانات المستخدم
 * @throws Exception في حالة عدم وجود صلاحية
 */
function checkUserPermission($allowedRoles = []) {
    // التحقق من وجود جلسة نشطة
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // التحقق من تسجيل دخول المستخدم
    if (!isset($_SESSION['user_id'])) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
            'data' => null
        ]);
        exit;
    }
    
    // التحقق من الأدوار المسموح لها
    if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode([
            'status' => false,
            'message' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة',
            'data' => null
        ]);
        exit;
    }
    
    // إرجاع بيانات المستخدم
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'name' => $_SESSION['full_name'] ?? '',
        'role' => $_SESSION['role'],
        'branch_id' => $_SESSION['branch_id'] ?? null,
        'permissions' => $_SESSION['permissions'] ?? []
    ];
}

/**
 * توليد رمز فريد للاستخدام في أرقام الفواتير أو رقم العميل
 * 
 * @param string $prefix بادئة الرمز
 * @param int $length طول الرمز الرقمي
 * @return string الرمز المولد
 */
function generateUniqueCode($prefix = '', $length = 8) {
    $timestamp = time();
    $random = mt_rand(100, 999);
    $unique = str_pad(substr($timestamp . $random, 0, $length), $length, '0', STR_PAD_LEFT);
    return $prefix . $unique;
}

/**
 * تحويل التاريخ من تنسيق إلى آخر
 * 
 * @param string $date التاريخ المراد تحويله
 * @param string $fromFormat تنسيق التاريخ الأصلي
 * @param string $toFormat تنسيق التاريخ المطلوب
 * @return string التاريخ بعد التحويل
 */
function formatDate($date, $fromFormat = 'Y-m-d', $toFormat = 'd/m/Y') {
    $datetime = DateTime::createFromFormat($fromFormat, $date);
    if ($datetime === false) {
        return $date;
    }
    return $datetime->format($toFormat);
}

/**
 * التحقق من صحة تنسيق التاريخ
 * 
 * @param string $date التاريخ المراد التحقق منه
 * @param string $format تنسيق التاريخ المطلوب
 * @return bool نتيجة التحقق
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * التحقق من صحة تنسيق الوقت
 * 
 * @param string $time الوقت المراد التحقق منه
 * @param string $format تنسيق الوقت المطلوب
 * @return bool نتيجة التحقق
 */
function validateTime($time, $format = 'H:i') {
    $d = DateTime::createFromFormat($format, $time);
    return $d && $d->format($format) === $time;
}

/**
 * تحويل رقم إلى تنسيق مالي
 * 
 * @param float $amount المبلغ المراد تنسيقه
 * @param int $decimals عدد الأرقام العشرية
 * @param string $currency رمز العملة
 * @return string المبلغ بعد التنسيق
 */
function formatCurrency($amount, $decimals = 2, $currency = 'ريال') {
    return number_format($amount, $decimals) . ' ' . $currency;
}

/**
 * حساب الفرق بين تاريخين بأيام أو ساعات أو دقائق
 * 
 * @param string $startDate تاريخ البداية
 * @param string $endDate تاريخ النهاية (افتراضيًا التاريخ الحالي)
 * @param string $unit وحدة القياس (days, hours, minutes)
 * @return int الفرق بالوحدة المحددة
 */
function dateDifference($startDate, $endDate = null, $unit = 'days') {
    $start = new DateTime($startDate);
    $end = $endDate ? new DateTime($endDate) : new DateTime();
    
    $interval = $start->diff($end);
    
    switch ($unit) {
        case 'days':
            return $interval->days;
        case 'hours':
            return $interval->h + ($interval->days * 24);
        case 'minutes':
            return $interval->i + ($interval->h * 60) + ($interval->days * 24 * 60);
        default:
            return $interval->days;
    }
}

/**
 * تحويل مصفوفة إلى CSV والتحميل للمستخدم
 * 
 * @param array $data البيانات المراد تصديرها
 * @param string $filename اسم الملف
 * @param array $headers عناوين الأعمدة
 */
function exportToCSV($data, $filename = 'export.csv', $headers = []) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    // إضافة علامة ترتيب البايت (BOM) لدعم الأحرف العربية في Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // كتابة عناوين الأعمدة
    if (!empty($headers)) {
        fputcsv($output, $headers);
    }
    
    // كتابة البيانات
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

/**
 * تسجيل نشاط في سجل النظام
 * 
 * @param int $userId معرف المستخدم
 * @param string $action نوع العملية
 * @param string $description وصف العملية
 * @param string $module الوحدة المتأثرة
 * @return bool نتيجة تسجيل النشاط
 */
function logActivity($userId, $action, $description, $module = '') {
    try {
        global $db;
        // التحقق من وجود اتصال بقاعدة البيانات
        if (!isset($db) || !$db) {
            $database = new Database();
            $db = $database->getConnection();
        }
        
        $query = "
            INSERT INTO activity_logs (user_id, action, description, module, ip_address, created_at)
            VALUES (:user_id, :action, :description, :module, :ip_address, NOW())
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":module", $module);
        $stmt->bindParam(":ip_address", $_SERVER['REMOTE_ADDR']);
        
        return $stmt->execute();
    } catch (Exception $e) {
        // في حالة حدوث خطأ، لا نريد تعطيل النظام
        return false;
    }
}

/**
 * التحقق من توفر موعد للحجز
 * 
 * @param int $employeeId معرف الموظف
 * @param string $date التاريخ
 * @param string $startTime وقت البداية
 * @param string $endTime وقت النهاية
 * @param int $appointmentId معرف الموعد للاستثناء عند التعديل
 * @return bool نتيجة التحقق
 */
function isTimeSlotAvailable($employeeId, $date, $startTime, $endTime, $appointmentId = 0) {
    try {
        global $db;
        if (!isset($db) || !$db) {
            $database = new Database();
            $db = $database->getConnection();
        }
        
        $query = "
            SELECT id FROM appointments 
            WHERE employee_id = :employee_id 
            AND appointment_date = :date 
            AND status IN ('scheduled', 'confirmed', 'in-progress')
            AND id != :appointment_id
            AND (
                (start_time <= :start_time AND end_time > :start_time) OR
                (start_time < :end_time AND end_time >= :end_time) OR
                (start_time >= :start_time AND end_time <= :end_time)
            )
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":employee_id", $employeeId);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":start_time", $startTime);
        $stmt->bindParam(":end_time", $endTime);
        $stmt->bindParam(":appointment_id", $appointmentId);
        $stmt->execute();
        
        return $stmt->rowCount() === 0;
    } catch (Exception $e) {
        // في حالة حدوث خطأ، نفترض أن الموعد غير متاح
        return false;
    }
}

/**
 * المساعدة في تنسيق الإستجابة
 * 
 * @param bool $status حالة العملية
 * @param string $message رسالة التنبيه
 * @param mixed $data البيانات المرتجعة
 * @return array مصفوفة الاستجابة
 */
function formatResponse($status, $message, $data = null) {
    return [
        "status" => $status,
        "message" => $message,
        "data" => $data
    ];
}
?>