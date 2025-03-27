<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <button class="btn btn-outline-secondary" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name'] ?? $_SESSION['name'] ?? 'المستخدم'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#" data-page="profile"><i class="fas fa-user me-1"></i> الملف الشخصي</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" id="logout-btn"><i class="fas fa-sign-out-alt me-1"></i> تسجيل الخروج</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger notification-badge">0</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="notification-dropdown">
                        <li><div class="dropdown-item text-center">لا توجد إشعارات</div></li>
                    </ul>
                </li>
            </ul>
            
            <div class="d-flex">
                <h4 class="mb-0">نظام إدارة الصالونات</h4>
            </div>
        </div>
    </div>
</nav>

<script>
    $(document).ready(function() {
        // تبديل القائمة الجانبية
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        
        // زر تسجيل الخروج
        $("#logout-btn").click(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'api/auth/logout.php',
                type: 'POST',
                success: function(response) {
                    if(response.status) {
                        window.location.href = 'login.php';
                    }
                }
            });
        });
        
        // تحميل الإشعارات
        loadNotifications();
        
        // تحميل الإشعارات كل دقيقة
        setInterval(loadNotifications, 60000);
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