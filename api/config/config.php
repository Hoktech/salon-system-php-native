<?php
/**
 * ملف الإعدادات الرئيسي للنظام
 * يحتوي على ثوابت النظام والإعدادات الأساسية
 */

// منع الوصول المباشر للملف
if (!defined('SALON_SYSTEM')) {
    die('الوصول المباشر لهذا الملف غير مسموح!');
}

// إعدادات الاتصال بقاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'salon_system');

// إعدادات المسارات
define('BASE_URL', 'http://localhost/salon-system'); // تغيير حسب مسار التطبيق الخاص بك
define('ROOT_PATH', dirname(dirname(dirname(__FILE__)))); // المسار الجذر للتطبيق
define('API_PATH', ROOT_PATH . '/api');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// إعدادات التطبيق
define('APP_NAME', 'نظام إدارة الصالونات');
define('APP_VERSION', '1.0.0');
define('DEFAULT_LANGUAGE', 'ar');
define('DEFAULT_THEME', 'default');
define('DEFAULT_TIMEZONE', 'Asia/Riyadh');
define('RECEIPT_WIDTH', 80); // عرض إيصال الطباعة بالملم
define('SESSION_TIMEOUT', 3600); // مدة الجلسة بالثواني (ساعة واحدة)

// إعدادات الأمان
define('HASH_SALT', 'salon_system_salt_' . date('Y'));
define('MIN_PASSWORD_LENGTH', 8);
define('ENABLE_CSRF', true);
define('CSRF_TOKEN_NAME', 'salon_csrf_token');

// إعدادات التاريخ والوقت
date_default_timezone_set(DEFAULT_TIMEZONE);

// تكوين معالج الأخطاء
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/error.log');

// إعداد تسجيل الأخطاء
if (!file_exists(ROOT_PATH . '/logs')) {
    mkdir(ROOT_PATH . '/logs', 0755, true);
}

/**
 * دالة تسجيل الأخطاء
 * 
 * @param string $message رسالة الخطأ
 * @param string $level مستوى الخطأ (error, warning, info)
 * @return void
 */
function logError($message, $level = 'error') {
    $logFile = ROOT_PATH . '/logs/' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp][$level] $message" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * إنشاء رمز CSRF
 *
 * @return string رمز CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * التحقق من صحة رمز CSRF
 *
 * @param string $token الرمز المراد التحقق منه
 * @return bool نتيجة التحقق
 */
function validateCsrfToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || $_SESSION[CSRF_TOKEN_NAME] !== $token) {
        return false;
    }
    return true;
}

/**
 * تأمين المدخلات
 *
 * @param string $input النص المراد تأمينه
 * @return string النص المؤمّن
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * تنسيق التاريخ والوقت حسب الصيغة المطلوبة
 *
 * @param string $datetime التاريخ والوقت
 * @param string $format صيغة التنسيق
 * @return string التاريخ والوقت المنسق
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    $date = new DateTime($datetime);
    return $date->format($format);
}

/**
 * تنسيق المبلغ المالي
 *
 * @param float $amount المبلغ
 * @param string $currency رمز العملة
 * @return string المبلغ المنسق
 */
function formatCurrency($amount, $currency = 'ر.س') {
    return number_format($amount, 2) . ' ' . $currency;
}

/**
 * إعادة توجيه المستخدم
 *
 * @param string $url رابط إعادة التوجيه
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * تحقق ما إذا كانت البيانات مرسلة بطريقة AJAX
 *
 * @return bool نتيجة التحقق
 */
function isAjaxRequest() {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * توليد رقم فاتورة فريد
 *
 * @param string $prefix بادئة الرقم
 * @return string رقم الفاتورة
 */
function generateInvoiceNumber($prefix = 'INV') {
    $timestamp = time();
    $random = mt_rand(1000, 9999);
    return $prefix . date('Ymd', $timestamp) . $random;
}

/**
 * تحميل فئات النظام الأساسية
 */
require_once API_PATH . '/config/Database.php';
require_once API_PATH . '/config/Auth.php';
require_once API_PATH . '/config/Response.php';
require_once API_PATH . '/config/Utils.php';

// إنشاء اتصال قاعدة البيانات
$db = Database::getInstance();

// إنشاء مثيل المصادقة
$auth = Auth::getInstance();

// بدء الجلسة إذا لم تكن قد بدأت بالفعل
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}