<div id="sidebar-wrapper">
    <div class="sidebar-brand">
        <h3><i class="fas fa-cut"></i></h3>
        <h6>نظام إدارة الصالونات</h6>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="#" class="nav-link" data-page="dashboard">
                <i class="fas fa-tachometer-alt"></i> لوحة التحكم
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="appointments">
                <i class="fas fa-calendar-alt"></i> المواعيد
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="customers">
                <i class="fas fa-users"></i> العملاء
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="invoices">
                <i class="fas fa-file-invoice-dollar"></i> الفواتير
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="services">
                <i class="fas fa-concierge-bell"></i> الخدمات
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="products">
                <i class="fas fa-shopping-basket"></i> المنتجات
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="employees">
                <i class="fas fa-user-tie"></i> الموظفين
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="expenses">
                <i class="fas fa-money-bill-wave"></i> المصروفات
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="reports">
                <i class="fas fa-chart-bar"></i> التقارير
            </a>
        </li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li>
            <a href="#" class="nav-link" data-page="branches">
                <i class="fas fa-store"></i> الفروع
            </a>
        </li>
        <li>
            <a href="#" class="nav-link" data-page="settings">
                <i class="fas fa-cog"></i> الإعدادات
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>