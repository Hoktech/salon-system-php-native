<div class="bg-dark" id="sidebar-wrapper">
    <div class="sidebar-heading text-center text-white">
        <i class="fas fa-cut me-2"></i> نظام إدارة الصالونات
    </div>
    <div class="list-group list-group-flush">
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="dashboard">
            <i class="fas fa-tachometer-alt ml-2"></i> لوحة التحكم
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="appointments">
            <i class="fas fa-calendar-alt ml-2"></i> المواعيد
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="customers">
            <i class="fas fa-users ml-2"></i> العملاء
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="invoices">
            <i class="fas fa-file-invoice-dollar ml-2"></i> الفواتير
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="services">
            <i class="fas fa-concierge-bell ml-2"></i> الخدمات
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="products">
            <i class="fas fa-shopping-basket ml-2"></i> المنتجات
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="employees">
            <i class="fas fa-user-tie ml-2"></i> الموظفين
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="expenses">
            <i class="fas fa-money-bill-wave ml-2"></i> المصروفات
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="reports">
            <i class="fas fa-chart-bar ml-2"></i> التقارير
        </a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="branches">
            <i class="fas fa-store ml-2"></i> الفروع
        </a>
        <a href="#" class="list-group-item list-group-item-action nav-link" data-page="settings">
            <i class="fas fa-cog ml-2"></i> الإعدادات
        </a>
        <?php endif; ?>
    </div>
</div>