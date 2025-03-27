<?php
// التحقق مما إذا كان المستخدم مسجل الدخول بالفعل
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة صالونات الحلاقة والكوافير</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.rtl.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            background-color: #fff;
            border-radius: 10px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-logo img {
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <h2>نظام إدارة الصالونات</h2>
        </div>
        
        <div id="login-alert" class="alert d-none"></div>
        
        <form id="login-form">
            <div class="mb-3">
                <label for="username" class="form-label">اسم المستخدم</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary" id="login-btn">تسجيل الدخول</button>
            </div>
        </form>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/api.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                
                const username = $('#username').val();
                const password = $('#password').val();
                
                // التحقق من إدخال البيانات
                if(!username || !password) {
                    showAlert('danger', 'يرجى إدخال اسم المستخدم وكلمة المرور');
                    return;
                }
                
                // تعطيل زر تسجيل الدخول
                $('#login-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري التحقق...');
                
                // إرسال طلب تسجيل الدخول
                $.ajax({
                    url: 'api/auth/login.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        username: username,
                        password: password
                    }),
                    success: function(response) {
                        if(response.status) {
                            showAlert('success', response.message);
                            // الانتقال إلى الصفحة الرئيسية
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 1000);
                        } else {
                            showAlert('danger', response.message);
                            $('#login-btn').prop('disabled', false).text('تسجيل الدخول');
                        }
                    },
                    error: function() {
                        showAlert('danger', 'حدث خطأ أثناء الاتصال بالخادم');
                        $('#login-btn').prop('disabled', false).text('تسجيل الدخول');
                    }
                });
            });
            
            // دالة لعرض التنبيهات
            function showAlert(type, message) {
                $('#login-alert').removeClass('d-none alert-success alert-danger alert-warning')
                    .addClass('alert-' + type)
                    .text(message);
            }
        });
    </script>
</body>
</html>