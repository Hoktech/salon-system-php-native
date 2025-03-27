<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-tachometer-alt"></i> لوحة التحكم</h2>
    </div>
    <div class="col-md-4 text-md-end">
        <button class="btn btn-danger" id="close-day-btn">
            <i class="fas fa-power-off"></i> إغلاق اليوم
        </button>
    </div>
</div>

<!-- البطاقات الإحصائية -->
<div class="row">
    <!-- إجمالي المبيعات -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-right-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            إجمالي المبيعات (اليوم)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-sales-today">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إجمالي المصروفات -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-right-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            إجمالي المصروفات (اليوم)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-expenses-today">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المواعيد -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-right-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            مواعيد اليوم</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="appointments-today">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- العملاء الجدد -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-right-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            عملاء جدد (هذا الشهر)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="new-customers">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مخطط المبيعات والمصروفات -->
<div class="row">
    <!-- مخطط المبيعات -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">المبيعات الشهرية</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">خيارات:</div>
                        <a class="dropdown-item" href="#" id="refresh-sales-chart">تحديث البيانات</a>
                        <a class="dropdown-item" href="#" id="download-sales-chart">تحميل كصورة</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- مخطط توزيع المبيعات -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">توزيع المبيعات</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">خيارات:</div>
                        <a class="dropdown-item" href="#" id="refresh-distribution-chart">تحديث البيانات</a>
                        <a class="dropdown-item" href="#" id="download-distribution-chart">تحميل كصورة</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="distributionChart"></canvas>
                </div>
                <div class="mt-4 text-center small" id="distribution-legend">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- الصف الأخير -->
<div class="row">
    <!-- المواعيد القادمة -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">المواعيد القادمة اليوم</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="upcoming-appointments-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>الوقت</th>
                                <th>العميل</th>
                                <th>الخدمة</th>
                                <th>الموظف</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- سيتم تحميل البيانات هنا عبر AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- تنبيهات المخزون -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">تنبيهات المخزون</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="inventory-alerts-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>المخزون الحالي</th>
                                <th>الحد الأدنى</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- سيتم تحميل البيانات هنا عبر AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تأكيد إغلاق اليوم -->
<div class="modal fade" id="closeDayModal" tabindex="-1" aria-labelledby="closeDayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closeDayModalLabel">تأكيد إغلاق اليوم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> تنبيه: بعد إغلاق اليوم، لن تتمكن من إضافة أو تعديل الفواتير والمصروفات لهذا اليوم.
                </div>
                <form id="close-day-form">
                    <div class="mb-3">
                        <label for="close-day-notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="close-day-notes" rows="3"></textarea>
                    </div>
                </form>

                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">ملخص اليوم</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>إجمالي المبيعات النقدية:</span>
                            <span id="cash-total-summary">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>إجمالي المبيعات بالبطاقة:</span>
                            <span id="card-total-summary">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>إجمالي المبيعات الأخرى:</span>
                            <span id="other-total-summary">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>إجمالي المصروفات:</span>
                            <span id="expenses-total-summary">0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>الصافي:</span>
                            <span id="net-total-summary">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirm-close-day-btn">تأكيد إغلاق اليوم</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // تحميل البيانات الأساسية
        loadDashboardData();
        
        // إنشاء المخططات
        initCharts();
        
        // زر إغلاق اليوم
        $('#close-day-btn').on('click', function() {
            // تحميل ملخص اليوم
            loadDaySummary();
            
            // عرض النافذة المنبثقة
            $('#closeDayModal').modal('show');
        });
        
        // زر تأكيد إغلاق اليوم
        $('#confirm-close-day-btn').on('click', function() {
            closeDay();
        });
        
        // تحديث البيانات كل 5 دقائق
        setInterval(loadDashboardData, 300000); // 5 دقائق = 300000 مللي ثانية
    });
    
    // دالة لتحميل بيانات لوحة التحكم
    function loadDashboardData() {
        // تحميل الإحصاءات
        loadStatistics();
        
        // تحميل المواعيد القادمة
        loadUpcomingAppointments();
        
        // تحميل تنبيهات المخزون
        loadInventoryAlerts();
        
        // تحديث المخططات
        updateCharts();
    }
    
    // دالة لتحميل الإحصاءات
    function loadStatistics() {
        $.ajax({
            url: 'api/dashboard/statistics.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const data = response.data;
                    
                    // تحديث الإحصاءات
                    $('#total-sales-today').text(data.total_sales_today.toFixed(2));
                    $('#total-expenses-today').text(data.total_expenses_today.toFixed(2));
                    $('#appointments-today').text(data.appointments_today);
                    $('#new-customers').text(data.new_customers);
                }
            }
        });
    }
    
    // دالة لتحميل المواعيد القادمة
    function loadUpcomingAppointments() {
        $.ajax({
            url: 'api/appointments/upcoming.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const appointments = response.data;
                    
                    if(appointments.length > 0) {
                        let html = '';
                        
                        appointments.forEach(appointment => {
                            // تنسيق الوقت
                            const time = appointment.start_time.substring(0, 5);
                            
                            // ترجمة الحالة
                            let statusClass = '';
                            let statusText = '';
                            switch(appointment.status) {
                                case 'scheduled':
                                    statusClass = 'secondary';
                                    statusText = 'مجدول';
                                    break;
                                case 'confirmed':
                                    statusClass = 'primary';
                                    statusText = 'مؤكد';
                                    break;
                                case 'completed':
                                    statusClass = 'success';
                                    statusText = 'مكتمل';
                                    break;
                                case 'cancelled':
                                    statusClass = 'danger';
                                    statusText = 'ملغي';
                                    break;
                                case 'no-show':
                                    statusClass = 'warning';
                                    statusText = 'لم يحضر';
                                    break;
                            }
                            
                            html += `
                                <tr>
                                    <td>${time}</td>
                                    <td>${appointment.customer_name}</td>
                                    <td>${appointment.service_name}</td>
                                    <td>${appointment.employee_name}</td>
                                    <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                                </tr>
                            `;
                        });
                        
                        $('#upcoming-appointments-table tbody').html(html);
                    } else {
                        $('#upcoming-appointments-table tbody').html('<tr><td colspan="5" class="text-center">لا توجد مواعيد قادمة</td></tr>');
                    }
                }
            }
        });
    }
    
    // دالة لتحميل تنبيهات المخزون
    function loadInventoryAlerts() {
        $.ajax({
            url: 'api/inventory/alerts.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const alerts = response.data;
                    
                    if(alerts.length > 0) {
                        let html = '';
                        
                        alerts.forEach(alert => {
                            // تحديد حالة المخزون
                            let statusClass = '';
                            let statusText = '';
                            
                            if(alert.stock_quantity <= 0) {
                                statusClass = 'danger';
                                statusText = 'نفد';
                            } else if(alert.stock_quantity < alert.minimum_quantity) {
                                statusClass = 'warning';
                                statusText = 'منخفض';
                            }
                            
                            html += `
                                <tr>
                                    <td>${alert.product_name}</td>
                                    <td>${alert.stock_quantity}</td>
                                    <td>${alert.minimum_quantity}</td>
                                    <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary restock-product" data-id="${alert.id}">
                                            <i class="fas fa-plus"></i> تعديل
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#inventory-alerts-table tbody').html(html);
                        
                        // إضافة مستمعات الأحداث
                        $('.restock-product').on('click', function() {
                            const productId = $(this).data('id');
                            // استدعاء دالة تعديل المخزون
                            window.location.href = 'index.php?page=products&action=restock&id=' + productId;
                        });
                    } else {
                        $('#inventory-alerts-table tbody').html('<tr><td colspan="5" class="text-center">لا توجد تنبيهات</td></tr>');
                    }
                }
            }
        });
    }
    
    // متغيرات عامة للمخططات
    let salesChart;
    let distributionChart;
    
    // دالة لإنشاء المخططات
    function initCharts() {
        // مخطط المبيعات
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'المبيعات',
                    data: [],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // مخطط التوزيع
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        distributionChart = new Chart(distributionCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)'
                    ],
                    hoverBackgroundColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(28, 200, 138, 1)',
                        'rgba(54, 185, 204, 1)',
                        'rgba(246, 194, 62, 1)',
                        'rgba(231, 74, 59, 1)'
                    ],
                    hoverBorderColor: 'rgba(255, 255, 255, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    // دالة لتحديث المخططات
    function updateCharts() {
        // تحديث مخطط المبيعات
        $.ajax({
            url: 'api/dashboard/sales_chart.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const data = response.data;
                    
                    // تحديث البيانات
                    salesChart.data.labels = data.labels;
                    salesChart.data.datasets[0].data = data.values;
                    salesChart.update();
                }
            }
        });
        
        // تحديث مخطط التوزيع
        $.ajax({
            url: 'api/dashboard/distribution_chart.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const data = response.data;
                    
                    // تحديث البيانات
                    distributionChart.data.labels = data.labels;
                    distributionChart.data.datasets[0].data = data.values;
                    distributionChart.update();
                    
                    // تحديث وسيلة الإيضاح
                    let legendHtml = '';
                    const colors = [
                        'primary', 'success', 'info', 'warning', 'danger'
                    ];
                    
                    data.labels.forEach((label, index) => {
                        legendHtml += `
                            <span class="me-2">
                                <i class="fas fa-circle text-${colors[index % colors.length]}"></i> ${label}
                            </span>
                        `;
                    });
                    
                    $('#distribution-legend').html(legendHtml);
                }
            }
        });
    }
    
    // دالة لتحميل ملخص اليوم
    function loadDaySummary() {
        $.ajax({
            url: 'api/dashboard/day_summary.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const data = response.data;
                    
                    // تحديث البيانات
                    $('#cash-total-summary').text(data.cash_total.toFixed(2));
                    $('#card-total-summary').text(data.card_total.toFixed(2));
                    $('#other-total-summary').text(data.other_total.toFixed(2));
                    $('#expenses-total-summary').text(data.expenses_total.toFixed(2));
                    $('#net-total-summary').text(data.net_total.toFixed(2));
                }
            }
        });
    }
    
    // دالة لإغلاق اليوم
    function closeDay() {
        const notes = $('#close-day-notes').val();
        
        // تعطيل الزر أثناء المعالجة
        $('#confirm-close-day-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري المعالجة...');
        
        $.ajax({
            url: 'api/day_end/close.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                notes: notes
            }),
            success: function(response) {
                if(response.status) {
                    // إغلاق النافذة المنبثقة
                    $('#closeDayModal').modal('hide');
                    
                    // عرض رسالة نجاح
                    alert('تم إغلاق اليوم بنجاح');
                    
                    // إعادة تحميل البيانات
                    loadDashboardData();
                } else {
                    alert('حدث خطأ أثناء إغلاق اليوم: ' + response.message);
                }
                
                // إعادة تفعيل الزر
                $('#confirm-close-day-btn').prop('disabled', false).text('تأكيد إغلاق اليوم');
            },
            error: function() {
                alert('حدث خطأ أثناء إغلاق اليوم');
                
                // إعادة تفعيل الزر
                $('#confirm-close-day-btn').prop('disabled', false).text('تأكيد إغلاق اليوم');
            }
        });
    }
</script>