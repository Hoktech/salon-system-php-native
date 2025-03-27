<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button class="btn btn-outline-secondary" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <a class="navbar-brand d-md-none mx-auto" href="index.php">نظام إدارة الصالونات</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="d-none d-md-block mx-auto">
                <h4 class="mb-0">نظام إدارة الصالونات</h4>
            </div>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge rounded-pill bg-danger notification-badge">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" id="notification-dropdown">
                        <li><div class="dropdown-item text-center">لا توجد إشعارات</div></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                        <span class="d-none d-md-inline-block me-1">
                            <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : (isset($_SESSION['name']) ? $_SESSION['name'] : 'المستخدم'); ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="loadPage('profile'); return false;"><i class="fas fa-user me-1"></i> الملف الشخصي</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" id="logout-btn"><i class="fas fa-sign-out-alt me-1"></i> تسجيل الخروج</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    $(document).ready(function() {
        // زر تسجيل الخروج
        $("#logout-btn").click(function(e) {
            e.preventDefault();
            
            if (confirm('هل أنت متأكد من رغبتك في تسجيل الخروج؟')) {
                $.ajax({
                    url: 'api/auth/logout.php',
                    type: 'POST',
                    success: function(response) {
                        window.location.href = 'login.php';
                    },
                    error: function() {
                        alert('حدث خطأ أثناء محاولة تسجيل الخروج');
                    }
                });
            }
        });
        
        // تحميل الإشعارات
        try {
            loadNotifications();
            // تحميل الإشعارات كل دقيقة
            setInterval(loadNotifications, 60000);
        } catch(e) {
            console.log('خطأ في تحميل الإشعارات:', e);
        }
    });
    
    // دالة لتحميل الإشعارات
    function loadNotifications() {
        $.ajax({
            url: 'api/notifications/read.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    updateNotifications(response.data);
                }
            },
            error: function() {
                // تجاهل الأخطاء في حالة عدم توفر API الإشعارات
            }
        });
    }
    
    // دالة لتحديث الإشعارات
    function updateNotifications(notifications) {
        const count = notifications.length;
        
        // تحديث عدد الإشعارات
        $('.notification-badge').text(count);
        
        // تحديث قائمة الإشعارات
        if(count > 0) {
            let html = '';
            
            notifications.forEach(function(notification) {
                html += `
                    <li>
                        <a class="dropdown-item notification-item" href="#" data-id="${notification.id}" data-type="${notification.type}">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="icon-circle ${getNotificationIconClass(notification.type)}">
                                        <i class="${getNotificationIcon(notification.type)}"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">${notification.date}</div>
                                    <span class="${notification.status == 'new' ? 'fw-bold' : ''}">${notification.message}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                `;
            });
            
            $('#notification-dropdown').html(html);
        } else {
            $('#notification-dropdown').html('<li><div class="dropdown-item text-center">لا توجد إشعارات</div></li>');
        }
    }
    
    // دالة للحصول على أيقونة الإشعار
    function getNotificationIcon(type) {
        switch(type) {
            case 'appointment':
                return 'fas fa-calendar-alt';
            case 'inventory':
                return 'fas fa-exclamation-triangle';
            case 'invoice':
                return 'fas fa-file-invoice-dollar';
            default:
                return 'fas fa-bell';
        }
    }
    
    // دالة للحصول على فئة أيقونة الإشعار
    function getNotificationIconClass(type) {
        switch(type) {
            case 'appointment':
                return 'bg-primary';
            case 'inventory':
                return 'bg-warning';
            case 'invoice':
                return 'bg-success';
            default:
                return 'bg-info';
        }
    }
</script>