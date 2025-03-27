<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="far fa-calendar-alt"></i> إدارة المواعيد</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" id="add-appointment-btn">
            <i class="fas fa-plus"></i> إضافة موعد جديد
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">التقويم</h6>
            </div>
            <div class="card-body">
                <div id="mini-calendar"></div>
                <hr>
                <div class="mb-2">
                    <h6 class="font-weight-bold mb-2">مفتاح الألوان:</h6>
                    <div class="d-flex align-items-center mb-1">
                        <div class="badge bg-primary" style="width: 20px; height: 20px; margin-left: 5px;"></div>
                        <span>مجدول</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div class="badge bg-success" style="width: 20px; height: 20px; margin-left: 5px;"></div>
                        <span>مؤكد</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div class="badge bg-info" style="width: 20px; height: 20px; margin-left: 5px;"></div>
                        <span>مكتمل</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div class="badge bg-warning" style="width: 20px; height: 20px; margin-left: 5px;"></div>
                        <span>لم يحضر</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="badge bg-danger" style="width: 20px; height: 20px; margin-left: 5px;"></div>
                        <span>ملغي</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold">مواعيد اليوم: <span id="current-date"></span></h6>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm" id="day-view-btn">يوم</button>
                    <button class="btn btn-outline-secondary btn-sm" id="week-view-btn">أسبوع</button>
                    <button class="btn btn-outline-secondary btn-sm" id="month-view-btn">شهر</button>
                </div>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold">قائمة الانتظار</h6>
                <button class="btn btn-sm btn-primary" id="add-waitlist-btn">
                    <i class="fas fa-plus"></i> إضافة عميل للانتظار
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="waitlist-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>الخدمة</th>
                                <th>الموظف</th>
                                <th>وقت الوصول</th>
                                <th>ملاحظات</th>
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

<!-- Modal إضافة/تعديل موعد -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentModalLabel">إضافة موعد جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="appointment-form">
                    <input type="hidden" id="appointment-id">
                    <div class="mb-3">
                        <label for="customer-select" class="form-label">العميل <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-select" id="customer-select" name="customer_id" required>
                                <option value="">اختر العميل</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="button" id="add-quick-customer-btn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="service-select" class="form-label">الخدمة <span class="text-danger">*</span></label>
                        <select class="form-select" id="service-select" name="service_id" required>
                            <option value="">اختر الخدمة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="employee-select" class="form-label">الموظف <span class="text-danger">*</span></label>
                        <select class="form-select" id="employee-select" name="employee_id" required>
                            <option value="">اختر الموظف</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointment-date" class="form-label">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="appointment-date" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointment-time" class="form-label">الوقت <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="appointment-time" name="time" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="appointment-duration" class="form-label">المدة (دقيقة)</label>
                        <input type="number" class="form-control" id="appointment-duration" name="duration" min="5" value="30">
                    </div>
                    <div class="mb-3">
                        <label for="appointment-status" class="form-label">الحالة</label>
                        <select class="form-select" id="appointment-status" name="status">
                            <option value="scheduled">مجدول</option>
                            <option value="confirmed">مؤكد</option>
                            <option value="completed">مكتمل</option>
                            <option value="cancelled">ملغي</option>
                            <option value="no-show">لم يحضر</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="appointment-notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="appointment-notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-appointment-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة عميل سريع -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-labelledby="quickCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickCustomerModalLabel">إضافة عميل سريع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quick-customer-form">
                    <div class="mb-3">
                        <label for="quick-customer-name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick-customer-name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quick-customer-phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="quick-customer-phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="quick-customer-gender" class="form-label">النوع <span class="text-danger">*</span></label>
                        <select class="form-select" id="quick-customer-gender" name="gender" required>
                            <option value="male">ذكر</option>
                            <option value="female">أنثى</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-quick-customer-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة عميل للانتظار -->
<div class="modal fade" id="waitlistModal" tabindex="-1" aria-labelledby="waitlistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="waitlistModalLabel">إضافة عميل للانتظار</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="waitlist-form">
                    <input type="hidden" id="waitlist-id">
                    <div class="mb-3">
                        <label for="waitlist-customer" class="form-label">العميل <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-select" id="waitlist-customer" name="customer_id" required>
                                <option value="">اختر العميل</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="button" id="add-waitlist-customer-btn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="waitlist-service" class="form-label">الخدمة <span class="text-danger">*</span></label>
                        <select class="form-select" id="waitlist-service" name="service_id" required>
                            <option value="">اختر الخدمة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="waitlist-employee" class="form-label">الموظف</label>
                        <select class="form-select" id="waitlist-employee" name="employee_id">
                            <option value="">أي موظف متاح</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="waitlist-notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="waitlist-notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-waitlist-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- تضمين المكتبات اللازمة -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js"></script>

<script>
    let calendar;
    let miniCalendar;
    let selectedDate = new Date();
    
    $(document).ready(function() {
        // تحديث التاريخ الحالي
        updateCurrentDate();
        
        // تهيئة التقويم
        initCalendar();
        
        // تهيئة التقويم المصغر
        initMiniCalendar();
        
        // تحميل العملاء
        loadCustomers();
        
        // تحميل الخدمات
        loadServices();
        
        // تحميل الموظفين
        loadEmployees();
        
        // تحميل قائمة الانتظار
        loadWaitlist();
        
        // تعيين التاريخ الحالي لحقل التاريخ
        $('#appointment-date').val(formatDate(new Date()));
        
        // زر إضافة موعد جديد
        $('#add-appointment-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#appointment-form')[0].reset();
            $('#appointment-id').val('');
            $('#appointment-date').val(formatDate(selectedDate));
            
            // تغيير عنوان النافذة المنبثقة
            $('#appointmentModalLabel').text('إضافة موعد جديد');
            
            // عرض النافذة المنبثقة
            $('#appointmentModal').modal('show');
        });
        
        // زر إضافة عميل سريع
        $('#add-quick-customer-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#quick-customer-form')[0].reset();
            
            // عرض النافذة المنبثقة
            $('#quickCustomerModal').modal('show');
        });
        
        // زر إضافة عميل للانتظار
        $('#add-waitlist-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#waitlist-form')[0].reset();
            $('#waitlist-id').val('');
            
            // عرض النافذة المنبثقة
            $('#waitlistModal').modal('show');
        });
        
        // زر إضافة عميل سريع للانتظار
        $('#add-waitlist-customer-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#quick-customer-form')[0].reset();
            
            // عرض النافذة المنبثقة
            $('#quickCustomerModal').modal('show');
        });
        
        // زر حفظ الموعد
        $('#save-appointment-btn').on('click', function() {
            saveAppointment();
        });
        
        // زر حفظ العميل السريع
        $('#save-quick-customer-btn').on('click', function() {
            saveQuickCustomer();
        });
        
        // زر حفظ عميل الانتظار
        $('#save-waitlist-btn').on('click', function() {
            saveWaitlist();
        });
        
        // التبديل بين طرق عرض التقويم
        $('#day-view-btn').on('click', function() {
            calendar.changeView('timeGridDay');
        });
        
        $('#week-view-btn').on('click', function() {
            calendar.changeView('timeGridWeek');
        });
        
        $('#month-view-btn').on('click', function() {
            calendar.changeView('dayGridMonth');
        });
        
        // تحديث مدة الموعد عند اختيار الخدمة
        $('#service-select').on('change', function() {
            const serviceId = $(this).val();
            
            if(serviceId) {
                // البحث عن الخدمة المحددة
                const service = window.servicesData.find(s => s.id == serviceId);
                if(service) {
                    $('#appointment-duration').val(service.duration);
                    
                    // تحديث قائمة الموظفين المتخصصين
                    updateSpecializedEmployees(serviceId);
                }
            }
        });
        
        // تحديث قائمة الموظفين المتخصصين للانتظار
        $('#waitlist-service').on('change', function() {
            const serviceId = $(this).val();
            
            if(serviceId) {
                updateWaitlistEmployees(serviceId);
            }
        });
    });
    
    // دالة لتحديث التاريخ الحالي
    function updateCurrentDate() {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        $('#current-date').text(selectedDate.toLocaleDateString('ar-SA', options));
    }
    
    // دالة لتهيئة التقويم
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ar',
            initialView: 'timeGridDay',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            direction: 'rtl',
            firstDay: 0, // الأحد هو أول يوم في الأسبوع
            slotMinTime: '09:00:00',
            slotMaxTime: '21:00:00',
            slotDuration: '00:15:00',
            allDaySlot: false,
            height: 'auto',
            selectable: true,
            selectMirror: true,
            navLinks: true,
            nowIndicator: true,
            dayMaxEvents: true,
            selectAllow: function(selectInfo) {
                // السماح باختيار الوقت فقط وليس الأيام
                return selectInfo.view.type.includes('timeGrid');
            },
            select: function(selectInfo) {
                // إعادة تعيين النموذج
                $('#appointment-form')[0].reset();
                $('#appointment-id').val('');
                
                // تعيين التاريخ والوقت
                const startDate = selectInfo.start;
                $('#appointment-date').val(formatDate(startDate));
                $('#appointment-time').val(formatTime(startDate));
                
                // حساب المدة الافتراضية
                const diffMs = selectInfo.end - selectInfo.start;
                const durationMinutes = Math.round(diffMs / 60000);
                $('#appointment-duration').val(durationMinutes);
                
                // تغيير عنوان النافذة المنبثقة
                $('#appointmentModalLabel').text('إضافة موعد جديد');
                
                // عرض النافذة المنبثقة
                $('#appointmentModal').modal('show');
                
                calendar.unselect();
            },
            eventClick: function(info) {
                // عرض تفاصيل الموعد
                const appointmentId = info.event.id;
                editAppointment(appointmentId);
            },
            eventClassNames: function(arg) {
                // تحديد لون الحدث حسب الحالة
                const status = arg.event.extendedProps.status;
                switch(status) {
                    case 'scheduled':
                        return ['bg-primary'];
                    case 'confirmed':
                        return ['bg-success'];
                    case 'completed':
                        return ['bg-info'];
                    case 'cancelled':
                        return ['bg-danger'];
                    case 'no-show':
                        return ['bg-warning'];
                    default:
                        return ['bg-primary'];
                }
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            datesSet: function(info) {
                selectedDate = info.start;
                updateCurrentDate();
                
                // تحديث التقويم المصغر
                miniCalendar.gotoDate(selectedDate);
                
                // تحميل المواعيد
                loadAppointments();
            }
        });
        
        calendar.render();
    }
    
    // دالة لتهيئة التقويم المصغر
    function initMiniCalendar() {
        const miniCalendarEl = document.getElementById('mini-calendar');
        
        miniCalendar = new FullCalendar.Calendar(miniCalendarEl, {
            locale: 'ar',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: ''
            },
            direction: 'rtl',
            firstDay: 0, // الأحد هو أول يوم في الأسبوع
            height: 'auto',
            navLinks: false,
            selectable: true,
            select: function(info) {
                const selectedDate = info.start;
                
                // تحديث التقويم الرئيسي
                calendar.gotoDate(selectedDate);
                
                miniCalendar.unselect();
            },
            datesSet: function(info) {
                // تحميل إحصائيات المواعيد
                loadAppointmentsStats(info.start);
            },
            dayCellDidMount: function(info) {
                // إضافة مؤشر لليوم الحالي
                if(isSameDay(info.date, new Date())) {
                    info.el.classList.add('fc-day-today');
                }
            }
        });
        
        miniCalendar.render();
    }
    
    // دالة لتحميل العملاء
    function loadCustomers() {
        // طلب العملاء من API
        getCustomers()
            .then(response => {
                if(response.status) {
                    const customers = response.data;
                    
                    if(customers.length > 0) {
                        let html = '<option value="">اختر العميل</option>';
                        
                        customers.forEach(customer => {
                            html += `<option value="${customer.id}">${customer.full_name} - ${customer.phone}</option>`;
                        });
                        
                        $('#customer-select').html(html);
                        $('#waitlist-customer').html(html);
                    }
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    
    // دالة لتحميل الخدمات
    function loadServices() {
        // طلب الخدمات من API
        getServices()
            .then(response => {
                if(response.status) {
                    // تخزين الخدمات في متغير عام
                    window.servicesData = response.data;
                    
                    if(window.servicesData.length > 0) {
                        let html = '<option value="">اختر الخدمة</option>';
                        
                        window.servicesData.forEach(service => {
                            if(service.active == 1) {
                                html += `<option value="${service.id}">${service.name} - ${service.price.toFixed(2)}</option>`;
                            }
                        });
                        
                        $('#service-select').html(html);
                        $('#waitlist-service').html(html);
                    }
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    
    // دالة لتحميل الموظفين
    function loadEmployees() {
        // طلب الموظفين من API
        getEmployees()
            .then(response => {
                if(response.status) {
                    // تخزين الموظفين في متغير عام
                    window.employeesData = response.data;
                    
                    if(window.employeesData.length > 0) {
                        let html = '<option value="">اختر الموظف</option>';
                        let waitlistHtml = '<option value="">أي موظف متاح</option>';
                        
                        window.employeesData.forEach(employee => {
                            if(employee.role === 'employee') {
                                html += `<option value="${employee.id}">${employee.full_name}</option>`;
                                waitlistHtml += `<option value="${employee.id}">${employee.full_name}</option>`;
                            }
                        });
                        
                        $('#employee-select').html(html);
                        $('#waitlist-employee').html(waitlistHtml);
                    }
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    
    // دالة لتحديث الموظفين المتخصصين
    function updateSpecializedEmployees(serviceId) {
        $.ajax({
            url: 'api/services/specialized_employees.php?service_id=' + serviceId,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const employeeIds = response.data;
                    
                    if(employeeIds.length > 0) {
                        let html = '<option value="">اختر الموظف</option>';
                        
                        window.employeesData.forEach(employee => {
                            if(employee.role === 'employee') {
                                // تحديد ما إذا كان الموظف متخصص
                                const isSpecialized = employeeIds.includes(parseInt(employee.id));
                                
                                // إضافة علامة للموظفين المتخصصين
                                const label = isSpecialized ? `${employee.full_name} ⭐` : employee.full_name;
                                
                                html += `<option value="${employee.id}">${label}</option>`;
                            }
                        });
                        
                        $('#employee-select').html(html);
                    }
                }
            }
        });
    }
    
    // دالة لتحديث الموظفين المتخصصين للانتظار
    function updateWaitlistEmployees(serviceId) {
        $.ajax({
            url: 'api/services/specialized_employees.php?service_id=' + serviceId,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const employeeIds = response.data;
                    
                    if(employeeIds.length > 0) {
                        let html = '<option value="">أي موظف متاح</option>';
                        
                        window.employeesData.forEach(employee => {
                            if(employee.role === 'employee') {
                                // تحديد ما إذا كان الموظف متخصص
                                const isSpecialized = employeeIds.includes(parseInt(employee.id));
                                
                                // إضافة علامة للموظفين المتخصصين
                                const label = isSpecialized ? `${employee.full_name} ⭐` : employee.full_name;
                                
                                html += `<option value="${employee.id}">${label}</option>`;
                            }
                        });
                        
                        $('#waitlist-employee').html(html);
                    }
                }
            }
        });
    }
    
    // دالة لتحميل المواعيد
    function loadAppointments() {
        // تنظيف التقويم
        calendar.removeAllEvents();
        
        // الحصول على نطاق التاريخ المعروض
        const start = formatDate(calendar.view.activeStart);
        const end = formatDate(calendar.view.activeEnd);
        
        // طلب المواعيد من API
        $.ajax({
            url: `api/appointments/read.php?start=${start}&end=${end}`,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const appointments = response.data;
                    
                    // إضافة المواعيد إلى التقويم
                    appointments.forEach(appointment => {
                        calendar.addEvent({
                            id: appointment.id,
                            title: getAppointmentTitle(appointment),
                            start: appointment.start_time,
                            end: appointment.end_time,
                            extendedProps: {
                                status: appointment.status,
                                customer: appointment.customer_name,
                                service: appointment.service_name,
                                employee: appointment.employee_name,
                                notes: appointment.notes
                            }
                        });
                    });
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    }
    
    // دالة لتحميل إحصائيات المواعيد
    function loadAppointmentsStats(monthStart) {
        const start = formatDate(monthStart);
        const end = formatDate(new Date(monthStart.getFullYear(), monthStart.getMonth() + 1, 0));
        
        $.ajax({
            url: `api/appointments/stats.php?start=${start}&end=${end}`,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const stats = response.data;
                    
                    // تحديث أيام التقويم المصغر
                    updateMiniCalendarDays(stats);
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    }
    
    // دالة لتحديث أيام التقويم المصغر
    function updateMiniCalendarDays(stats) {
        // إعادة تعيين تنسيق الأيام
        $('.fc-daygrid-day').each(function() {
            $(this).find('.fc-daygrid-day-number').css('background-color', '');
        });
        
        // إضافة مؤشرات للأيام التي تحتوي على مواعيد
        stats.forEach(day => {
            const date = new Date(day.date);
            const dayNumber = date.getDate();
            const dayElement = $(`.fc-day-top[data-date="${formatDate(date)}"]`);
            
            if(dayElement.length > 0) {
                dayElement.find('.fc-day-number').css('background-color', getBadgeColor(day.count));
            } else {
                // بحث بطريقة أخرى إذا لم يتم العثور على العنصر
                $(`.fc-daygrid-day[data-date="${formatDate(date)}"]`).find('.fc-daygrid-day-number').css('background-color', getBadgeColor(day.count));
            }
        });
    }
    
    // دالة للحصول على لون المؤشر حسب عدد المواعيد
    function getBadgeColor(count) {
        if(count <= 0) {
            return '';
        } else if(count < 5) {
            return 'rgba(0, 123, 255, 0.3)';
        } else if(count < 10) {
            return 'rgba(0, 123, 255, 0.5)';
        } else {
            return 'rgba(0, 123, 255, 0.7)';
        }
    }
    
    // دالة لتحميل قائمة الانتظار
    function loadWaitlist() {
        $.ajax({
            url: 'api/waitlist/read.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const waitlist = response.data;
                    
                    if(waitlist.length > 0) {
                        let html = '';
                        
                        waitlist.forEach(item => {
                            // تنسيق وقت الوصول
                            const arrivalTime = new Date(item.arrival_time).toLocaleTimeString('ar-SA', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            
                            html += `
                                <tr>
                                    <td>${item.customer_name}</td>
                                    <td>${item.customer_phone}</td>
                                    <td>${item.service_name}</td>
                                    <td>${item.employee_name || 'أي موظف متاح'}</td>
                                    <td>${arrivalTime}</td>
                                    <td>${item.notes || '-'}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary create-appointment-btn" data-id="${item.id}">
                                            <i class="fas fa-calendar-plus"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info edit-waitlist-btn" data-id="${item.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-waitlist-btn" data-id="${item.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#waitlist-table tbody').html(html);
                        
                        // إضافة مستمعات الأحداث
                        $('.create-appointment-btn').on('click', function() {
                            const waitlistId = $(this).data('id');
                            createAppointmentFromWaitlist(waitlistId);
                        });
                        
                        $('.edit-waitlist-btn').on('click', function() {
                            const waitlistId = $(this).data('id');
                            editWaitlist(waitlistId);
                        });
                        
                        $('.delete-waitlist-btn').on('click', function() {
                            const waitlistId = $(this).data('id');
                            deleteWaitlist(waitlistId);
                        });
                    } else {
                        $('#waitlist-table tbody').html('<tr><td colspan="7" class="text-center">لا يوجد عملاء في قائمة الانتظار</td></tr>');
                    }
                } else {
                    $('#waitlist-table tbody').html('<tr><td colspan="7" class="text-center text-danger">حدث خطأ أثناء تحميل قائمة الانتظار</td></tr>');
                }
            },
            error: function(error) {
                console.error(error);
                $('#waitlist-table tbody').html('<tr><td colspan="7" class="text-center text-danger">حدث خطأ أثناء تحميل قائمة الانتظار</td></tr>');
            }
        });
    }
    
    // دالة لحفظ الموعد
    function saveAppointment() {
        // التحقق من صحة النموذج
        if(!$('#appointment-form')[0].checkValidity()) {
            $('#appointment-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات الموعد
        const appointmentId = $('#appointment-id').val();
        const customerId = $('#customer-select').val();
        const serviceId = $('#service-select').val();
        const employeeId = $('#employee-select').val();
        const date = $('#appointment-date').val();
        const time = $('#appointment-time').val();
        const duration = $('#appointment-duration').val();
        const status = $('#appointment-status').val();
        const notes = $('#appointment-notes').val();
        
        // حساب وقت النهاية
        const startDateTime = new Date(`${date}T${time}`);
        const endDateTime = new Date(startDateTime.getTime() + duration * 60000);
        const endTime = formatTime(endDateTime);
        
        // إنشاء كائن البيانات
        const appointmentData = {
            customer_id: customerId,
            service_id: serviceId,
            employee_id: employeeId,
            date: date,
            start_time: time,
            end_time: endTime,
            status: status,
            notes: notes
        };
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-appointment-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // حفظ الموعد
        if(appointmentId) {
            // تعديل موعد موجود
            appointmentData.id = appointmentId;
            
            $.ajax({
                url: 'api/appointments/update.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(appointmentData),
                success: function(response) {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#appointmentModal').modal('hide');
                        
                        // إعادة تحميل المواعيد
                        loadAppointments();
                        
                        // عرض رسالة نجاح
                        alert('تم تعديل الموعد بنجاح');
                    } else {
                        alert('حدث خطأ أثناء تعديل الموعد: ' + response.message);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-appointment-btn').prop('disabled', false).text('حفظ');
                },
                error: function(error) {
                    alert('حدث خطأ أثناء تعديل الموعد');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-appointment-btn').prop('disabled', false).text('حفظ');
                }
            });
        } else {
            // إضافة موعد جديد
            $.ajax({
                url: 'api/appointments/create.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(appointmentData),
                success: function(response) {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#appointmentModal').modal('hide');
                        
                        // إعادة تحميل المواعيد
                        loadAppointments();
                        
                        // عرض رسالة نجاح
                        alert('تم إضافة الموعد بنجاح');
                    } else {
                        alert('حدث خطأ أثناء إضافة الموعد: ' + response.message);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-appointment-btn').prop('disabled', false).text('حفظ');
                },
                error: function(error) {
                    alert('حدث خطأ أثناء إضافة الموعد');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-appointment-btn').prop('disabled', false).text('حفظ');
                }
            });
        }
    }
    
    // دالة لحفظ العميل السريع
    function saveQuickCustomer() {
        // التحقق من صحة النموذج
        if(!$('#quick-customer-form')[0].checkValidity()) {
            $('#quick-customer-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات العميل
        const customerData = {
            full_name: $('#quick-customer-name').val(),
            phone: $('#quick-customer-phone').val(),
            gender: $('#quick-customer-gender').val()
        };
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-quick-customer-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // إضافة العميل
        $.ajax({
            url: 'api/customers/create.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(customerData),
            success: function(response) {
                if(response.status) {
                    // إغلاق النافذة المنبثقة
                    $('#quickCustomerModal').modal('hide');
                    
                    // إعادة تحميل العملاء
                    loadCustomers();
                    
                    // عرض رسالة نجاح
                    alert('تم إضافة العميل بنجاح');
                    
                    // تحديث قائمة العملاء وتحديد العميل الجديد
                    const customerId = response.customer_id;
                    const customerOption = `<option value="${customerId}">${customerData.full_name} - ${customerData.phone}</option>`;
                    
                    $('#customer-select').append(customerOption);
                    $('#customer-select').val(customerId);
                    
                    $('#waitlist-customer').append(customerOption);
                    $('#waitlist-customer').val(customerId);
                } else {
                    alert('حدث خطأ أثناء إضافة العميل: ' + response.message);
                }
                
                // إعادة تفعيل زر الحفظ
                $('#save-quick-customer-btn').prop('disabled', false).text('حفظ');
            },
            error: function(error) {
                alert('حدث خطأ أثناء إضافة العميل');
                console.error(error);
                
                // إعادة تفعيل زر الحفظ
                $('#save-quick-customer-btn').prop('disabled', false).text('حفظ');
            }
        });
    }
    
    // دالة لحفظ عميل الانتظار
    function saveWaitlist() {
        // التحقق من صحة النموذج
        if(!$('#waitlist-form')[0].checkValidity()) {
            $('#waitlist-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات الانتظار
        const waitlistId = $('#waitlist-id').val();
        const waitlistData = {
            customer_id: $('#waitlist-customer').val(),
            service_id: $('#waitlist-service').val(),
            employee_id: $('#waitlist-employee').val() || null,
            notes: $('#waitlist-notes').val()
        };
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-waitlist-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        if(waitlistId) {
            // تعديل عميل موجود في قائمة الانتظار
            waitlistData.id = waitlistId;
            
            $.ajax({
                url: 'api/waitlist/update.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(waitlistData),
                success: function(response) {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#waitlistModal').modal('hide');
                        
                        // إعادة تحميل قائمة الانتظار
                        loadWaitlist();
                        
                        // عرض رسالة نجاح
                        alert('تم تعديل عميل الانتظار بنجاح');
                    } else {
                        alert('حدث خطأ أثناء تعديل عميل الانتظار: ' + response.message);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-waitlist-btn').prop('disabled', false).text('حفظ');
                },
                error: function(error) {
                    alert('حدث خطأ أثناء تعديل عميل الانتظار');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-waitlist-btn').prop('disabled', false).text('حفظ');
                }
            });
        } else {
            // إضافة عميل جديد لقائمة الانتظار
            $.ajax({
                url: 'api/waitlist/create.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(waitlistData),
                success: function(response) {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#waitlistModal').modal('hide');
                        
                        // إعادة تحميل قائمة الانتظار
                        loadWaitlist();
                        
                        // عرض رسالة نجاح
                        alert('تم إضافة العميل لقائمة الانتظار بنجاح');
                    } else {
                        alert('حدث خطأ أثناء إضافة العميل لقائمة الانتظار: ' + response.message);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-waitlist-btn').prop('disabled', false).text('حفظ');
                },
                error: function(error) {
                    alert('حدث خطأ أثناء إضافة العميل لقائمة الانتظار');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-waitlist-btn').prop('disabled', false).text('حفظ');
                }
            });
        }
    }
    
    // دالة لتعديل موعد
    function editAppointment(appointmentId) {
        $.ajax({
            url: 'api/appointments/read_one.php?id=' + appointmentId,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const appointment = response.data;
                    
                    // تعبئة النموذج ببيانات الموعد
                    $('#appointment-id').val(appointment.id);
                    $('#customer-select').val(appointment.customer_id);
                    $('#service-select').val(appointment.service_id);
                    $('#employee-select').val(appointment.employee_id);
                    
                    // تعيين التاريخ والوقت
                    const startTime = new Date(appointment.start_time);
                    $('#appointment-date').val(formatDate(startTime));
                    $('#appointment-time').val(formatTime(startTime));
                    
                    // حساب المدة
                    const endTime = new Date(appointment.end_time);
                    const diffMs = endTime - startTime;
                    const durationMinutes = Math.round(diffMs / 60000);
                    $('#appointment-duration').val(durationMinutes);
                    
                    $('#appointment-status').val(appointment.status);
                    $('#appointment-notes').val(appointment.notes);
                    
                    // تحديث الموظفين المتخصصين
                    updateSpecializedEmployees(appointment.service_id);
                    
                    // تغيير عنوان النافذة المنبثقة
                    $('#appointmentModalLabel').text('تعديل الموعد');
                    
                    // عرض النافذة المنبثقة
                    $('#appointmentModal').modal('show');
                } else {
                    alert('حدث خطأ أثناء تحميل بيانات الموعد: ' + response.message);
                }
            },
            error: function(error) {
                alert('حدث خطأ أثناء تحميل بيانات الموعد');
                console.error(error);
            }
        });
    }
    
    // دالة لتعديل عميل في قائمة الانتظار
    function editWaitlist(waitlistId) {
        $.ajax({
            url: 'api/waitlist/read_one.php?id=' + waitlistId,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const waitlist = response.data;
                    
                    // تعبئة النموذج ببيانات الانتظار
                    $('#waitlist-id').val(waitlist.id);
                    $('#waitlist-customer').val(waitlist.customer_id);
                    $('#waitlist-service').val(waitlist.service_id);
                    $('#waitlist-employee').val(waitlist.employee_id || '');
                    $('#waitlist-notes').val(waitlist.notes);
                    
                    // تحديث الموظفين المتخصصين
                    updateWaitlistEmployees(waitlist.service_id);
                    
                    // تغيير عنوان النافذة المنبثقة
                    $('#waitlistModalLabel').text('تعديل عميل في قائمة الانتظار');
                    
                    // عرض النافذة المنبثقة
                    $('#waitlistModal').modal('show');
                } else {
                    alert('حدث خطأ أثناء تحميل بيانات الانتظار: ' + response.message);
                }
            },
            error: function(error) {
                alert('حدث خطأ أثناء تحميل بيانات الانتظار');
                console.error(error);
            }
        });
    }
    
    // دالة لحذف عميل من قائمة الانتظار
    function deleteWaitlist(waitlistId) {
        if(confirm('هل أنت متأكد من حذف هذا العميل من قائمة الانتظار؟')) {
            $.ajax({
                url: 'api/waitlist/delete.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: waitlistId }),
                success: function(response) {
                    if(response.status) {
                        // إعادة تحميل قائمة الانتظار
                        loadWaitlist();
                        
                        // عرض رسالة نجاح
                        alert('تم حذف العميل من قائمة الانتظار بنجاح');
                    } else {
                        alert('حدث خطأ أثناء حذف العميل من قائمة الانتظار: ' + response.message);
                    }
                },
                error: function(error) {
                    alert('حدث خطأ أثناء حذف العميل من قائمة الانتظار');
                    console.error(error);
                }
            });
        }
    }
    
    // دالة لإنشاء موعد من قائمة الانتظار
    function createAppointmentFromWaitlist(waitlistId) {
        $.ajax({
            url: 'api/waitlist/read_one.php?id=' + waitlistId,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const waitlist = response.data;
                    
                    // إعادة تعيين النموذج
                    $('#appointment-form')[0].reset();
                    $('#appointment-id').val('');
                    
                    // تعبئة النموذج ببيانات الانتظار
                    $('#customer-select').val(waitlist.customer_id);
                    $('#service-select').val(waitlist.service_id);
                    $('#employee-select').val(waitlist.employee_id || '');
                    $('#appointment-notes').val(waitlist.notes);
                    
                    // تعيين التاريخ الحالي
                    $('#appointment-date').val(formatDate(new Date()));
                    
                    // تعيين الوقت الحالي
                    const now = new Date();
                    $('#appointment-time').val(formatTime(now));
                    
                    // تعيين المدة حسب الخدمة
                    const service = window.servicesData.find(s => s.id == waitlist.service_id);
                    if(service) {
                        $('#appointment-duration').val(service.duration);
                    }
                    
                    // تحديث الموظفين المتخصصين
                    updateSpecializedEmployees(waitlist.service_id);
                    
                    // تغيير عنوان النافذة المنبثقة
                    $('#appointmentModalLabel').text('إنشاء موعد من قائمة الانتظار');
                    
                    // تخزين معرف الانتظار
                    $('#appointment-form').data('waitlist-id', waitlistId);
                    
                    // عرض النافذة المنبثقة
                    $('#appointmentModal').modal('show');
                    
                    // استبدال دالة حفظ الموعد
                    $('#save-appointment-btn').off('click').on('click', function() {
                        saveAppointmentFromWaitlist(waitlistId);
                    });
                } else {
                    alert('حدث خطأ أثناء تحميل بيانات الانتظار: ' + response.message);
                }
            },
            error: function(error) {
                alert('حدث خطأ أثناء تحميل بيانات الانتظار');
                console.error(error);
            }
        });
    }
    
    // دالة لحفظ موعد من قائمة الانتظار
    function saveAppointmentFromWaitlist(waitlistId) {
        // التحقق من صحة النموذج
        if(!$('#appointment-form')[0].checkValidity()) {
            $('#appointment-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات الموعد
        const customerId = $('#customer-select').val();
        const serviceId = $('#service-select').val();
        const employeeId = $('#employee-select').val();
        const date = $('#appointment-date').val();
        const time = $('#appointment-time').val();
        const duration = $('#appointment-duration').val();
        const status = $('#appointment-status').val();
        const notes = $('#appointment-notes').val();
        
        // حساب وقت النهاية
        const startDateTime = new Date(`${date}T${time}`);
        const endDateTime = new Date(startDateTime.getTime() + duration * 60000);
        const endTime = formatTime(endDateTime);
        
        // إنشاء كائن البيانات
        const appointmentData = {
            customer_id: customerId,
            service_id: serviceId,
            employee_id: employeeId,
            date: date,
            start_time: time,
            end_time: endTime,
            status: status,
            notes: notes,
            waitlist_id: waitlistId // إضافة معرف الانتظار
        };
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-appointment-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // إضافة الموعد وحذف العميل من قائمة الانتظار
        $.ajax({
            url: 'api/appointments/create_from_waitlist.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(appointmentData),
            success: function(response) {
                if(response.status) {
                    // إغلاق النافذة المنبثقة
                    $('#appointmentModal').modal('hide');
                    
                    // إعادة تحميل المواعيد
                    loadAppointments();
                    
                    // إعادة تحميل قائمة الانتظار
                    loadWaitlist();
                    
                    // عرض رسالة نجاح
                    alert('تم إنشاء الموعد وحذف العميل من قائمة الانتظار بنجاح');
                } else {
                    alert('حدث خطأ أثناء إنشاء الموعد: ' + response.message);
                }
                
                // إعادة تفعيل زر الحفظ وإرجاع الدالة الأصلية
                $('#save-appointment-btn').prop('disabled', false).text('حفظ');
                $('#save-appointment-btn').off('click').on('click', saveAppointment);
            },
            error: function(error) {
                alert('حدث خطأ أثناء إنشاء الموعد');
                console.error(error);
                
                // إعادة تفعيل زر الحفظ وإرجاع الدالة الأصلية
                $('#save-appointment-btn').prop('disabled', false).text('حفظ');
                $('#save-appointment-btn').off('click').on('click', saveAppointment);
            }
        });
    }
    
    // دالة للحصول على عنوان الموعد
    function getAppointmentTitle(appointment) {
        const customerName = appointment.customer_name;
        const serviceName = appointment.service_name;
        
        return `${customerName} - ${serviceName}`;
    }
    
    // دالة لتنسيق التاريخ
    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }
    
    // دالة لتنسيق الوقت
    function formatTime(date) {
        return date.toTimeString().slice(0, 5);
    }
    
    // دالة للتحقق من تطابق اليوم
    function isSameDay(date1, date2) {
        return date1.getFullYear() === date2.getFullYear() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getDate() === date2.getDate();
    }
</script>