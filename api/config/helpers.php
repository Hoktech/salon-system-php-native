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
        throw new Exception("يجب تسجيل الدخول أولاً");
    }
    
    // التحقق من الأدوار المسموح لها
    if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
        throw new Exception("ليس لديك صلاحية للوصول إلى هذه الصفحة");
    }
    
    // إرجاع بيانات المستخدم
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'name' => $_SESSION['name'],
        'role' => $_SESSION['role'],
        'branch_id' => $_SESSION['branch_id'],
        'permissions' => $_SESSION['permissions'] ?? []
    ];
}

/**
 * التحقق من الصلاحية المحددة للمستخدم
 * 
 * @param string $permission الصلاحية المطلوبة
 * @return bool نتيجة التحقق
 * @throws Exception في حالة عدم وجود صلاحية
 */
function checkPermission($permission) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // التحقق من تسجيل دخول المستخدم
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("يجب تسجيل الدخول أولاً");
    }
    
    // الصلاحيات للمستخدم الحالي
    $userPermissions = $_SESSION['permissions'] ?? [];
    
    // إذا كان المستخدم مدير (admin) فلديه جميع الصلاحيات
    if ($_SESSION['role'] === 'admin') {
        return true;
    }
    
    // التحقق من وجود الصلاحية المطلوبة
    if (!in_array($permission, $userPermissions)) {
        throw new Exception("ليس لديك صلاحية للقيام بهذه العملية");
    }
    
    return true;
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
 * إرسال بريد إلكتروني باستخدام PHPMailer إذا كان متاحاً
 * 
 * @param string $to عنوان البريد الإلكتروني للمستلم
 * @param string $subject موضوع الرسالة
 * @param string $body محتوى الرسالة
 * @param array $attachments مرفقات الرسالة
 * @return bool نتيجة إرسال البريد
 */
function sendEmail($to, $subject, $body, $attachments = []) {
    // التحقق من وجود إعدادات SMTP في ملف الإعدادات
    if (!defined('SMTP_HOST') || !defined('SMTP_USERNAME') || !defined('SMTP_PASSWORD')) {
        return false;
    }
    
    // التحقق من وجود مكتبة PHPMailer
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return false;
    }
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'tls';
        $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom(SMTP_USERNAME, defined('COMPANY_NAME') ? COMPANY_NAME : 'نظام إدارة الصالونات');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $body;
        
        // إضافة المرفقات
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $mail->addAttachment($attachment);
                }
            }
        }
        
        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
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
 * التحقق من كمية المنتج المتوفرة في المخزون
 * 
 * @param int $productId معرف المنتج
 * @param int $quantity الكمية المطلوبة
 * @param int $branchId معرف الفرع (اختياري)
 * @return bool نتيجة التحقق
 */
function isProductAvailable($productId, $quantity, $branchId = 0) {
    try {
        global $db;
        if (!isset($db) || !$db) {
            $database = new Database();
            $db = $database->getConnection();
        }
        
        $query = "SELECT id, quantity FROM products WHERE id = :id";
        
        // إضافة شرط الفرع إذا كان محدداً
        if ($branchId > 0) {
            $query = "
                SELECT p.id, i.quantity 
                FROM products p
                JOIN inventory i ON p.id = i.product_id
                WHERE p.id = :id AND i.branch_id = :branch_id
            ";
        }
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $productId);
        
        if ($branchId > 0) {
            $stmt->bindParam(":branch_id", $branchId);
        }
        
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            return false;
        }
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product['quantity'] >= $quantity;
    } catch (Exception $e) {
        // في حالة حدوث خطأ، نفترض أن المنتج غير متوفر
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

/**
 * إنشاء رابط تأكيد يحتوي على توقيع رقمي
 * 
 * @param string $action نوع العملية
 * @param int $id المعرف
 * @param string $secret مفتاح سري
 * @param int $expiry مدة الصلاحية بالثواني
 * @return string الرابط المولد
 */
function generateSignedUrl($action, $id, $secret = null, $expiry = 3600) {
    if ($secret === null && defined('APP_SECRET_KEY')) {
        $secret = APP_SECRET_KEY;
    } elseif ($secret === null) {
        $secret = 'salonmanagementsystem';
    }
    
    $expires = time() + $expiry;
    $data = $action . '|' . $id . '|' . $expires;
    $signature = hash_hmac('sha256', $data, $secret);
    
    return $action . '?id=' . $id . '&expires=' . $expires . '&signature=' . $signature;
}

/**
 * التحقق من صحة التوقيع الرقمي للرابط
 * 
 * @param string $action نوع العملية
 * @param int $id المعرف
 * @param int $expires وقت انتهاء الصلاحية
 * @param string $signature التوقيع
 * @param string $secret المفتاح السري
 * @return bool نتيجة التحقق
 */
function verifySignedUrl($action, $id, $expires, $signature, $secret = null) {
    if ($secret === null && defined('APP_SECRET_KEY')) {
        $secret = APP_SECRET_KEY;
    } elseif ($secret === null) {
        $secret = 'salonmanagementsystem';
    }
    
    // التحقق من انتهاء صلاحية الرابط
    if (time() > $expires) {
        return false;
    }
    
    $data = $action . '|' . $id . '|' . $expires;
    $expectedSignature = hash_hmac('sha256', $data, $secret);
    
    return hash_equals($expectedSignature, $signature);
}
?>