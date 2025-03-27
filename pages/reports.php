<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-chart-bar"></i> التقارير</h2>
    </div>
</div>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link <?php echo ($report_type == 'sales') ? 'active' : ''; ?>" href="#" data-report="sales">تقارير المبيعات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($report_type == 'expenses') ? 'active' : ''; ?>" href="#" data-report="expenses">تقارير المصروفات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($report_type == 'services') ? 'active' : ''; ?>" href="#" data-report="services">تقارير الخدمات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($report_type == 'employees') ? 'active' : ''; ?>" href="#" data-report="employees">تقارير الموظفين</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($report_type == 'customers') ? 'active' : ''; ?>" href="#" data-report="customers">تقارير العملاء</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($report_type == 'branches') ? 'active' : ''; ?>" href="#" data-report="branches">تقارير الفروع</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div id="report-filters" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="date-range" class="form-label">النطاق الزمني</label>
                    <select class="form-select" id="date-range">
                        <option value="today">اليوم</option>
                        <option value="yesterday">الأمس</option>
                        <option value="week">هذا الأسبوع</option>
                        <option value="month" selected>هذا الشهر</option>
                        <option value="year">هذه السنة</option>
                        <option value="custom">مخصص</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start-date" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="start-date">
                </div>
                <div class="col-md-3">
                    <label for="end-date" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="end-date">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" id="generate-report">
                        <i class="fas fa-sync-alt me-1"></i> إنشاء التقرير
                    </button>
                </div>
            </div>
            
            <!-- فلاتر إضافية حسب نوع التقرير -->
            <div id="additional-filters" class="mt-3">
                <!-- سيتم تحميل الفلاتر الإضافية هنا باستخدام JavaScript -->
            </div>
        </div>
        
        <div id="report-content">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- قوالب الفلاتر الإضافية -->
<template id="sales-filters-template">
    <div class="row g-3">
        <div class="col-md-3">
            <label for="payment-method" class="form-label">طريقة الدفع</label>
            <select class="form-select" id="payment-method">
                <option value="">جميع الطرق</option>
                <option value="cash">نقدي</option>
                <option value="card">بطاقة</option>
                <option value="bank_transfer">تحويل بنكي</option>
                <option value="other">أخرى</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="payment-status" class="form-label">حالة الدفع</label>
            <select class="form-select" id="payment-status">
                <option value="">جميع الحالات</option>
                <option value="paid">مدفوع</option>
                <option value="pending">معلق</option>
                <option value="cancelled">ملغي</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="cashier" class="form-label">الكاشير</label>
            <select class="form-select" id="cashier">
                <option value="">جميع الكاشير</option>
                <!-- سيتم تحميل الكاشير بواسطة AJAX -->
            </select>
        </div>
        <div class="col-md-3">
            <label for="group-by" class="form-label">تجميع حسب</label>
            <select class="form-select" id="group-by">
                <option value="day">اليوم</option>
                <option value="week">الأسبوع</option>
                <option value="month">الشهر</option>
                <option value="cashier">الكاشير</option>
                <option value="payment_method">طريقة الدفع</option>
            </select>
        </div>
    </div>
</template>

<template id="expenses-filters-template">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="expense-category" class="form-label">فئة المصروفات</label>
            <select class="form-select" id="expense-category">
                <option value="">جميع الفئات</option>
                <!-- سيتم تحميل الفئات بواسطة AJAX -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="expense-user" class="form-label">المستخدم</label>
            <select class="form-select" id="expense-user">
                <option value="">جميع المستخدمين</option>
                <!-- سيتم تحميل المستخدمين بواسطة AJAX -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="expense-group-by" class="form-label">تجميع حسب</label>
            <select class="form-select" id="expense-group-by">
                <option value="day">اليوم</option>
                <option value="category">الفئة</option>
                <option value="user">المستخدم</option>
                <option value="month">الشهر</option>
            </select>
        </div>
    </div>
</template>

<template id="services-filters-template">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="service-category" class="form-label">فئة الخدمات</label>
            <select class="form-select" id="service-category">
                <option value="">جميع الفئات</option>
                <!-- سيتم تحميل الفئات بواسطة AJAX -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="service-employee" class="form-label">الموظف</label>
            <select class="form-select" id="service-employee">
                <option value="">جميع الموظفين</option>
                <!-- سيتم تحميل الموظفين بواسطة AJAX -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="service-group-by" class="form-label">تجميع حسب</label>
            <select class="form-select" id="service-group-by">
                <option value="service">الخدمة</option>
                <option value="category">الفئة</option>
                <option value="employee">الموظف</option>
                <option value="day">اليوم</option>
                <option value="month">الشهر</option>
            </select>
        </div>
    </div>
</template>

<template id="employees-filters-template">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="employee" class="form-label">الموظف</label>
            <select class="form-select" id="employee">
                <option value="">جميع الموظفين</option>
                <!-- سيتم تحميل الموظفين بواسطة AJAX -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="employee-metric" class="form-label">المقياس</label>
            <select class="form-select" id="employee-metric">
                <option value="service_count">عدد الخدمات</option>
                <option value="revenue">الإيرادات</option>
                <option value="customer_count">عدد العملاء</option>
                <option value="working_hours">ساعات العمل</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="employee-group-by" class="form-label">تجميع حسب</label>
            <select class="form-select" id="employee-group-by">
                <option value="employee">الموظف</option>
                <option value="day">اليوم</option>
                <option value="week">الأسبوع</option>
                <option value="month">الشهر</option>
            </select>
        </div>
    </div>
</template>

<template id="customers-filters-template">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="customer-gender" class="form-label">الجنس</label>
            <select class="form-select" id="customer-gender">
                <option value="">الكل</option>
                <option value="male">ذكر</option>
                <option value="female">أنثى</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="customer-metric" class="form-label">المقياس</label>
            <select class="form-select" id="customer-metric">
                <option value="visit_count">عدد الزيارات</option>
                <option value="total_spent">إجمالي الإنفاق</option>
                <option value="avg_spent">متوسط الإنفاق</option>
                <option value="new_customers">عملاء جدد</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="customer-group-by" class="form-label">تجميع حسب</label>
            <select class="form-select" id="customer-group-by">
                <option value="day">اليوم</option>
                <option value="week">الأسبوع</option>
                <option value="month">الشهر</option>
                <option value="gender">الجنس</option>
            </select>
        </div>
    </div>
</template>

<template id="branches-filters-template">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="branch" class="form-label">الفرع</label>
            <select class="form-select" id="branch">
                <option value="">جميع الفروع</option>
                <!-- سيتم تحميل الفروع بواسطة AJAX -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="branch-metric" class="form-label">المقياس</label>
            <select class="form-select" id="branch-metric">
                <option value="revenue">الإيرادات</option>
                <option value="expenses">المصروفات</option>
                <option value="profit">الربح</option>
                <option value="service_count">عدد الخدمات</option>
                <option value="customer_count">عدد العملاء</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="branch-group-by" class="form-label">تجميع حسب</label>
            <select class="form-select" id="branch-group-by">
                <option value="branch">الفرع</option>
                <option value="day">اليوم</option>
                <option value="week">الأسبوع</option>
                <option value="month">الشهر</option>
            </select>
        </div>
    </div>
</template>

<script>
    $(document).ready(function() {
        // تحديد النطاق الزمني الافتراضي (هذا الشهر)
        setDefaultDateRange();
        
        // تحميل التقرير الافتراضي (المبيعات)
        loadReportFilters('sales');
        loadReport();
        
        // تبديل نوع التقرير
        $('.nav-link').on('click', function(e) {
            e.preventDefault();
            
            // تحديث التبويب النشط
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            
            // تحميل فلاتر التقرير المناسبة
            const reportType = $(this).data('report');
            loadReportFilters(reportType);
            
            // إعادة تحميل التقرير
            loadReport();
        });
        
        // تغيير النطاق الزمني
        $('#date-range').on('change', function() {
            if ($(this).val() === 'custom') {
                $('#start-date, #end-date').prop('disabled', false);
            } else {
                $('#start-date, #end-date').prop('disabled', true);
                setDateRange($(this).val());
            }
        });
        
        // توليد التقرير
        $('#generate-report').on('click', function() {
            loadReport();
        });
        
        // دالة لتحميل فلاتر التقرير
        function loadReportFilters(reportType) {
            // تحميل قالب الفلاتر المناسب
            const template = document.getElementById(`${reportType}-filters-template`);
            if (template) {
                $('#additional-filters').html(template.innerHTML);
                
                // تحميل البيانات الإضافية حسب نوع التقرير
                switch (reportType) {
                    case 'sales':
                        loadCashiers();
                        break;
                    case 'expenses':
                        loadExpenseCategories();
                        loadUsers();
                        break;
                    case 'services':
                        loadServiceCategories();
                        loadEmployees();
                        break;
                    case 'employees':
                        loadEmployees();
                        break;
                    case 'branches':
                        loadBranches();
                        break;
                }
            } else {
                $('#additional-filters').html('');
            }
        }
        
        // دالة لتحميل التقرير
        function loadReport() {
            // عرض مؤشر التحميل
            $('#report-content').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">جاري التحميل...</span></div></div>');
            
            // تحديد نوع التقرير النشط
            const reportType = $('.nav-link.active').data('report');
            
            // تجميع بيانات الفلاتر
            const filters = {
                type: reportType,
                start_date: $('#start-date').val(),
                end_date: $('#end-date').val()
            };
            
            // إضافة الفلاتر الإضافية حسب نوع التقرير
            switch (reportType) {
                case 'sales':
                    filters.payment_method = $('#payment-method').val();
                    filters.payment_status = $('#payment-status').val();
                    filters.cashier = $('#cashier').val();
                    filters.group_by = $('#group-by').val();
                    break;
                case 'expenses':
                    filters.category = $('#expense-category').val();
                    filters.user = $('#expense-user').val();
                    filters.group_by = $('#expense-group-by').val();
                    break;
                case 'services':
                    filters.category = $('#service-category').val();
                    filters.employee = $('#service-employee').val();
                    filters.group_by = $('#service-group-by').val();
                    break;
                case 'employees':
                    filters.employee = $('#employee').val();
                    filters.metric = $('#employee-metric').val();
                    filters.group_by = $('#employee-group-by').val();
                    break;
                case 'customers':
                    filters.gender = $('#customer-gender').val();
                    filters.metric = $('#customer-metric').val();
                    filters.group_by = $('#customer-group-by').val();
                    break;
                case 'branches':
                    filters.branch = $('#branch').val();
                    filters.metric = $('#branch-metric').val();
                    filters.group_by = $('#branch-group-by').val();
                    break;
            }
            
            // طلب التقرير من API
            $.ajax({
                url: 'api/reports/generate.php',
                type: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderReport(response.data, reportType);
                    } else {
                        $('#report-content').html(`<div class="alert alert-danger">${response.message}</div>`);
                    }
                },
                error: function(xhr, status, error) {
                    $('#report-content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل التقرير</div>');
                    console.error(error);
                }
            });
        }
        
        // دالة لعرض التقرير
        function renderReport(data, reportType) {
            // إنشاء محتوى التقرير
            let html = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>${getReportTitle(reportType)}</h4>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" id="export-pdf">
                            <i class="fas fa-file-pdf me-1"></i> تصدير PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" id="export-excel">
                            <i class="fas fa-file-excel me-1"></i> تصدير Excel
                        </button>
                    </div>
                </div>
            `;
            
            // عرض الملخص
            if (data.summary) {
                html += `
                    <div class="row mb-4">
                        ${renderSummaryCards(data.summary, reportType)}
                    </div>
                `;
            }
            
            // عرض الرسم البياني
            if (data.chart) {
                html += `
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">الرسم البياني</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="reportChart"></canvas>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // عرض الجدول
            if (data.table && data.table.rows && data.table.rows.length > 0) {
                html += `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">تفاصيل التقرير</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            ${renderTableHeader(data.table.columns)}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${renderTableRows(data.table.rows, data.table.columns)}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html += '<div class="alert alert-info">لا توجد بيانات للعرض في النطاق الزمني المحدد</div>';
            }
            
            // عرض التقرير
            $('#report-content').html(html);
            
            // إنشاء الرسم البياني إذا كانت البيانات متوفرة
            if (data.chart) {
                createChart(data.chart);
            }
            
            // إضافة أحداث للأزرار
            $('#export-pdf').on('click', function() {
                exportReport('pdf', reportType);
            });
            
            $('#export-excel').on('click', function() {
                exportReport('excel', reportType);
            });
        }
        
        // دالة لعرض بطاقات الملخص
        function renderSummaryCards(summary, reportType) {
            let html = '';
            let icons = {
                'total': 'fa-money-bill-wave',
                'count': 'fa-hashtag',
                'average': 'fa-chart-line',
                'profit': 'fa-hand-holding-usd',
                'expenses': 'fa-file-invoice',
                'customers': 'fa-users'
            };
            
            Object.keys(summary).forEach(function(key, index) {
                const colors = ['primary', 'success', 'info', 'warning'];
                const color = colors[index % colors.length];
                
                html += `
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-${color} shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-${color} text-uppercase mb-1">
                                            ${getSummaryTitle(key, reportType)}
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            ${formatSummaryValue(key, summary[key], reportType)}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas ${icons[key] || 'fa-chart-bar'} fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            return html;
        }
        
        // دالة لعرض عناوين جدول التقرير
        function renderTableHeader(columns) {
            let html = '';
            
            columns.forEach(function(column) {
                html += `<th>${getColumnTitle(column)}</th>`;
            });
            
            return html;
        }
        
        // دالة لعرض صفوف جدول التقرير
        function renderTableRows(rows, columns) {
            let html = '';
            
            rows.forEach(function(row) {
                html += '<tr>';
                
                columns.forEach(function(column) {
                    html += `<td>${formatCellValue(row[column], column)}</td>`;
                });
                
                html += '</tr>';
            });
            
            return html;
        }
        
        // دالة لإنشاء الرسم البياني
        function createChart(chartData) {
            const ctx = document.getElementById('reportChart').getContext('2d');
            
            // تحديد نوع الرسم البياني
            const chartType = chartData.type || 'bar';
            
            // إنشاء الرسم البياني
            new Chart(ctx, {
                type: chartType,
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // دالة لتصدير التقرير
        function exportReport(format, reportType) {
            // تجميع بيانات الفلاتر
            const filters = {
                type: reportType,
                start_date: $('#start-date').val(),
                end_date: $('#end-date').val(),
                format: format
            };
            
            // إضافة الفلاتر الإضافية حسب نوع التقرير
            switch (reportType) {
                case 'sales':
                    filters.payment_method = $('#payment-method').val();
                    filters.payment_status = $('#payment-status').val();
                    filters.cashier = $('#cashier').val();
                    filters.group_by = $('#group-by').val();
                    break;
                case 'expenses':
                    filters.category = $('#expense-category').val();
                    filters.user = $('#expense-user').val();
                    filters.group_by = $('#expense-group-by').val();
                    break;
                case 'services':
                    filters.category = $('#service-category').val();
                    filters.employee = $('#service-employee').val();
                    filters.group_by = $('#service-group-by').val();
                    break;
                case 'employees':
                    filters.employee = $('#employee').val();
                    filters.metric = $('#employee-metric').val();
                    filters.group_by = $('#employee-group-by').val();
                    break;
                case 'customers':
                    filters.gender = $('#customer-gender').val();
                    filters.metric = $('#customer-metric').val();
                    filters.group_by = $('#customer-group-by').val();
                    break;
                case 'branches':
                    filters.branch = $('#branch').val();
                    filters.metric = $('#branch-metric').val();
                    filters.group_by = $('#branch-group-by').val();
                    break;
            }
            
            // بناء URL التصدير
            const queryString = $.param(filters);
            const exportUrl = `api/reports/export.php?${queryString}`;
            
            // فتح URL التصدير في نافذة جديدة
            window.open(exportUrl, '_blank');
        }
        
        // دالة لتحميل الكاشير
        function loadCashiers() {
            $.ajax({
                url: 'api/users/list.php',
                type: 'GET',
                data: { role: 'cashier' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">جميع الكاشير</option>';
                        
                        response.data.forEach(function(cashier) {
                            options += `<option value="${cashier.id}">${cashier.full_name}</option>`;
                        });
                        
                        $('#cashier').html(options);
                    }
                }
            });
        }
        
        // دالة لتحميل فئات المصروفات
        function loadExpenseCategories() {
            $.ajax({
                url: 'api/expenses/categories.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">جميع الفئات</option>';
                        
                        response.data.forEach(function(category) {
                            options += `<option value="${category.id}">${category.name}</option>`;
                        });
                        
                        $('#expense-category').html(options);
                    }
                }
            });
        }
        
// دالة لتحميل المستخدمين
function loadUsers() {
            $.ajax({
                url: 'api/users/list.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">جميع المستخدمين</option>';
                        
                        response.data.forEach(function(user) {
                            options += `<option value="${user.id}">${user.full_name}</option>`;
                        });
                        
                        $('#expense-user').html(options);
                    }
                }
            });
        }
        
        // دالة لتحميل فئات الخدمات
        function loadServiceCategories() {
            $.ajax({
                url: 'api/services/categories.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">جميع الفئات</option>';
                        
                        response.data.forEach(function(category) {
                            options += `<option value="${category}">${category}</option>`;
                        });
                        
                        $('#service-category').html(options);
                    }
                }
            });
        }
        
        // دالة لتحميل الموظفين
        function loadEmployees() {
            $.ajax({
                url: 'api/users/list.php',
                type: 'GET',
                data: { role: 'employee' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">جميع الموظفين</option>';
                        
                        response.data.forEach(function(employee) {
                            options += `<option value="${employee.id}">${employee.full_name}</option>`;
                        });
                        
                        // تحديث عناصر التحكم المناسبة
                        $('#service-employee, #employee').html(options);
                    }
                }
            });
        }
        
        // دالة لتحميل الفروع
        function loadBranches() {
            $.ajax({
                url: 'api/branches/list.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">جميع الفروع</option>';
                        
                        response.data.forEach(function(branch) {
                            options += `<option value="${branch.id}">${branch.name}</option>`;
                        });
                        
                        $('#branch').html(options);
                    }
                }
            });
        }
        
        // دالة لتعيين النطاق الزمني الافتراضي
        function setDefaultDateRange() {
            // تعيين النطاق الافتراضي (هذا الشهر)
            setDateRange('month');
            
            // تعطيل حقول التاريخ المخصص افتراضيًا
            $('#start-date, #end-date').prop('disabled', true);
        }
        
        // دالة لتعيين نطاق زمني محدد
        function setDateRange(range) {
            const today = new Date();
            let startDate, endDate;
            
            switch (range) {
                case 'today':
                    startDate = today;
                    endDate = today;
                    break;
                case 'yesterday':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 1);
                    endDate = startDate;
                    break;
                case 'week':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - today.getDay());
                    endDate = today;
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = new Date(today.getFullYear(), 11, 31);
                    break;
                default:
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            }
            
            // تنسيق التاريخ بصيغة YYYY-MM-DD
            $('#start-date').val(formatDate(startDate));
            $('#end-date').val(formatDate(endDate));
        }
        
        // دالة لتنسيق التاريخ
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            
            return `${year}-${month}-${day}`;
        }
        
        // دالة للحصول على عنوان التقرير
        function getReportTitle(reportType) {
            const titles = {
                'sales': 'تقرير المبيعات',
                'expenses': 'تقرير المصروفات',
                'services': 'تقرير الخدمات',
                'employees': 'تقرير أداء الموظفين',
                'customers': 'تقرير العملاء',
                'branches': 'تقرير الفروع'
            };
            
            return titles[reportType] || 'تقرير';
        }
        
        // دالة للحصول على عنوان الملخص
        function getSummaryTitle(key, reportType) {
            const titles = {
                // المبيعات
                'total': 'إجمالي المبيعات',
                'count': 'عدد الفواتير',
                'average': 'متوسط قيمة الفاتورة',
                'discount': 'إجمالي الخصومات',
                
                // المصروفات
                'expenses_total': 'إجمالي المصروفات',
                'expenses_count': 'عدد المصروفات',
                'expenses_average': 'متوسط المصروفات',
                
                // الخدمات
                'services_total': 'إجمالي إيرادات الخدمات',
                'services_count': 'عدد الخدمات المقدمة',
                'services_average': 'متوسط سعر الخدمة',
                
                // الموظفين
                'employee_services': 'عدد الخدمات',
                'employee_revenue': 'إجمالي الإيرادات',
                'employee_customers': 'عدد العملاء',
                'employee_hours': 'ساعات العمل',
                
                // العملاء
                'customers_total': 'إجمالي العملاء',
                'new_customers': 'العملاء الجدد',
                'visit_count': 'عدد الزيارات',
                'customer_spending': 'إجمالي الإنفاق',
                
                // الفروع
                'branch_revenue': 'إجمالي الإيرادات',
                'branch_expenses': 'إجمالي المصروفات',
                'branch_profit': 'صافي الربح',
                'branch_services': 'عدد الخدمات',
                'branch_customers': 'عدد العملاء'
            };
            
            // عناوين حسب نوع التقرير
            if (reportType === 'sales') {
                if (key === 'total') return 'إجمالي المبيعات';
                if (key === 'count') return 'عدد الفواتير';
                if (key === 'average') return 'متوسط قيمة الفاتورة';
                if (key === 'discount') return 'إجمالي الخصومات';
            } else if (reportType === 'expenses') {
                if (key === 'total') return 'إجمالي المصروفات';
                if (key === 'count') return 'عدد المصروفات';
                if (key === 'average') return 'متوسط المصروفات';
            }
            
            return titles[key] || key;
        }
        
        // دالة لتنسيق قيمة الملخص
        function formatSummaryValue(key, value, reportType) {
            // تنسيق القيم المالية
            if (key.includes('total') || key.includes('revenue') || key.includes('expenses') || 
                key.includes('profit') || key.includes('average') || key.includes('spending') || 
                key.includes('discount')) {
                return formatCurrency(value);
            }
            
            // تنسيق العداد
            if (key.includes('count') || key.includes('services') || key.includes('customers')) {
                return formatNumber(value);
            }
            
            // تنسيق الساعات
            if (key.includes('hours')) {
                return `${formatNumber(value)} ساعة`;
            }
            
            return value;
        }
        
        // دالة للحصول على عنوان العمود
        function getColumnTitle(column) {
            const titles = {
                'date': 'التاريخ',
                'day': 'اليوم',
                'week': 'الأسبوع',
                'month': 'الشهر',
                'year': 'السنة',
                'cashier': 'الكاشير',
                'employee': 'الموظف',
                'service': 'الخدمة',
                'category': 'الفئة',
                'gender': 'الجنس',
                'branch': 'الفرع',
                'payment_method': 'طريقة الدفع',
                'payment_status': 'حالة الدفع',
                'count': 'العدد',
                'total': 'الإجمالي',
                'average': 'المتوسط',
                'discount': 'الخصم',
                'revenue': 'الإيرادات',
                'expenses': 'المصروفات',
                'profit': 'الربح',
                'net': 'الصافي',
                'percentage': 'النسبة المئوية',
                'duration': 'المدة',
                'visits': 'الزيارات',
                'amount': 'المبلغ',
                'services_count': 'عدد الخدمات',
                'customers_count': 'عدد العملاء',
                'working_hours': 'ساعات العمل'
            };
            
            return titles[column] || column;
        }
        
        // دالة لتنسيق قيمة الخلية
        function formatCellValue(value, column) {
            if (value === null || value === undefined) {
                return '-';
            }
            
            // تنسيق التواريخ
            if (column === 'date' || column === 'day' || column === 'week' || column === 'month' || column === 'year') {
                return value;
            }
            
            // تنسيق القيم المالية
            if (column === 'total' || column === 'average' || column === 'discount' || 
                column === 'revenue' || column === 'expenses' || column === 'profit' || 
                column === 'net' || column === 'amount') {
                return formatCurrency(value);
            }
            
            // تنسيق النسب المئوية
            if (column === 'percentage') {
                return `${value}%`;
            }
            
            // تنسيق طريقة الدفع
            if (column === 'payment_method') {
                const methods = {
                    'cash': 'نقدي',
                    'card': 'بطاقة',
                    'bank_transfer': 'تحويل بنكي',
                    'other': 'أخرى'
                };
                
                return methods[value] || value;
            }
            
            // تنسيق حالة الدفع
            if (column === 'payment_status') {
                const statuses = {
                    'paid': 'مدفوع',
                    'pending': 'معلق',
                    'cancelled': 'ملغي'
                };
                
                return statuses[value] || value;
            }
            
            // تنسيق الجنس
            if (column === 'gender') {
                return value === 'male' ? 'ذكر' : (value === 'female' ? 'أنثى' : value);
            }
            
            // تنسيق المدة
            if (column === 'duration') {
                return `${value} دقيقة`;
            }
            
            // تنسيق ساعات العمل
            if (column === 'working_hours') {
                return `${value} ساعة`;
            }
            
            // تنسيق الأعداد
            if (column === 'count' || column === 'visits' || column === 'services_count' || column === 'customers_count') {
                return formatNumber(value);
            }
            
            return value;
        }
        
        // دالة لتنسيق المبالغ المالية
        function formatCurrency(amount) {
            return parseFloat(amount).toFixed(2) + ' ر.س';
        }
        
        // دالة لتنسيق الأرقام
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    });
</script>