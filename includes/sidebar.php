<div class="bg-light border-left" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4">
        <h4>نظام إدارة الصالونات</h4>
    </div>
    <div class="list-group list-group-flush">
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="dashboard">
            <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="appointments">
            <i class="far fa-calendar-alt me-2"></i> المواعيد
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="customers">
            <i class="fas fa-users me-2"></i> العملاء
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="invoices">
            <i class="fas fa-file-invoice-dollar me-2"></i> الفواتير
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="services">
            <i class="fas fa-cut me-2"></i> الخدمات
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="products">
            <i class="fas fa-shopping-cart me-2"></i> المنتجات
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="employees">
            <i class="fas fa-user-tie me-2"></i> الموظفين
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="expenses">
            <i class="fas fa-money-bill-wave me-2"></i> المصروفات
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="reports">
            <i class="fas fa-chart-bar me-2"></i> التقارير
        </a>
        <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'): ?>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="branches">
            <i class="fas fa-store me-2"></i> الفروع
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="settings">
            <i class="fas fa-cog me-2"></i> الإعدادات
        </a>
        <?php endif; ?>
    </div>
</div>