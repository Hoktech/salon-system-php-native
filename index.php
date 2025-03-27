<?php
// التحقق من تسجيل الدخول
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة صالونات الحلاقة والكوافير</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.rtl.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Google Fonts (Tajawal) -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- هيكل الصفحة -->
    <div class="d-flex" id="wrapper">
        <!-- القائمة الجانبية -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- محتوى الصفحة -->
        <div id="page-content-wrapper">
            <!-- الهيدر -->
            <?php include 'includes/header.php'; ?>
            
            <!-- المحتوى الرئيسي -->
            <div class="container-fluid p-4">
                <div id="main-content">
                    <!-- سيتم تحميل المحتوى هنا من خلال AJAX -->
                </div>
            </div>
            
            <!-- تذييل الصفحة -->
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/api.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/sidebar-toggle.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    
    <script>
        $(document).ready(function() {
            // تحميل لوحة التحكم افتراضياً
            loadPage('dashboard');
            
            // إضافة مستمع للنقر على عناصر القائمة
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                loadPage(page);
            });
        });
        
        // دالة لتحميل الصفحات
        function loadPage(page) {
            $.ajax({
                url: 'pages/' + page + '.php',
                type: 'GET',
                beforeSend: function() {
                    $('#main-content').html(`
                        <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    `);
                },
                success: function(data) {
                    $('#main-content').html(data);
                    
                    // تنشيط الرابط الحالي في القائمة
                    $('.nav-link').removeClass('active');
                    $('.nav-link[data-page="' + page + '"]').addClass('active');
                    
                    // تحديث عنوان الصفحة
                    const pageTitle = $('.nav-link[data-page="' + page + '"]').text().trim();
                    updatePageTitle(pageTitle);
                    
                    // إغلاق القائمة الجانبية في الأجهزة الصغيرة بعد اختيار صفحة
                    if ($(window).width() < 768) {
                        $("#wrapper").addClass("toggled");
                    }
                },
                error: function() {
                    $('#main-content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل الصفحة</div>');
                }
            });
        }
        
        // دالة لتحديث عنوان الصفحة
        function updatePageTitle(title) {
            document.title = title + ' - نظام إدارة صالونات الحلاقة والكوافير';
        }
    </script>
</body>
</html>