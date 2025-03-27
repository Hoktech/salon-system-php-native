<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-cog"></i> إعدادات النظام</h2>
    </div>
</div>




<div class="card shadow mb-4">
    <div class="card-header py-3">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link <?php echo ($action == 'general') ? 'active' : ''; ?>" href="#" data-action="general">إعدادات عامة</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($action == 'users') ? 'active' : ''; ?>" href="#" data-action="users">المستخدمين</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($action == 'permissions') ? 'active' : ''; ?>" href="#" data-action="permissions">الصلاحيات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($action == 'invoice') ? 'active' : ''; ?>" href="#" data-action="invoice">إعدادات الفواتير</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($action == 'backup') ? 'active' : ''; ?>" href="#" data-action="backup">النسخ الاحتياطي</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div id="settings-content">
            <!-- سيتم تحميل محتوى الإعدادات هنا حسب التبويب المحدد -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- قالب إعدادات عامة -->
<template id="general-settings-template">
    <form id="general-settings-form">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">معلومات الصالون</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="salon-name" class="form-label">اسم الصالون</label>
                            <input type="text" class="form-control" id="salon-name" name="salon_name">
                        </div>
                        <div class="mb-3">
                            <label for="salon-phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" id="salon-phone" name="salon_phone">
                        </div>
                        <div class="mb-3">
                            <label for="salon-email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="salon-email" name="salon_email">
                        </div>
                        <div class="mb-3">
                            <label for="salon-address" class="form-label">العنوان</label>
                            <textarea class="form-control" id="salon-address" name="salon_address" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="salon-logo" class="form-label">شعار الصالون</label>
                            <input type="file" class="form-control" id="salon-logo" name="salon_logo">
                            <div id="logo-preview" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">إعدادات النظام</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="currency" class="form-label">العملة</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="SAR">ريال سعودي (ر.س)</option>
                                <option value="USD">دولار أمريكي ($)</option>
                                <option value="EUR">يورو (€)</option>
                                <option value="AED">درهم إماراتي (د.إ)</option>
                                <option value="EGP">جنيه مصري (ج.م)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tax-rate" class="form-label">نسبة الضريبة (%)</label>
                            <input type="number" class="form-control" id="tax-rate" name="tax_rate" min="0" max="100" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="appointment-duration" class="form-label">مدة الموعد الافتراضية (دقيقة)</label>
                            <input type="number" class="form-control" id="appointment-duration" name="appointment_duration" min="5" step="5">
                        </div>
                        <div class="mb-3">
                            <label for="working-hours-start" class="form-label">بداية ساعات العمل</label>
                            <input type="time" class="form-control" id="working-hours-start" name="working_hours_start">
                        </div>
                        <div class="mb-3">
                            <label for="working-hours-end" class="form-label">نهاية ساعات العمل</label>
                            <input type="time" class="form-control" id="working-hours-end" name="working_hours_end">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="enable-online-booking" name="enable_online_booking">
                            <label class="form-check-label" for="enable-online-booking">
                                تفعيل الحجز عبر الإنترنت
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary" id="save-general-settings">
                <i class="fas fa-save me-1"></i> حفظ الإعدادات
            </button>
        </div>
    </form>
</template>

<!-- قالب إدارة المستخدمين -->
<template id="users-settings-template">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>إدارة المستخدمين</h5>
        <button class="btn btn-primary" id="add-user-btn">
            <i class="fas fa-plus"></i> إضافة مستخدم جديد
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="users-table" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>اسم المستخدم</th>
                    <th>الاسم الكامل</th>
                    <th>البريد الإلكتروني</th>
                    <th>الدور</th>
                    <th>الفرع</th>
                    <th>الحالة</th>
                    <th>آخر تسجيل دخول</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <!-- سيتم تحميل البيانات هنا عبر AJAX -->
            </tbody>
        </table>
    </div>
</template>

<!-- قالب إدارة الصلاحيات -->
<template id="permissions-settings-template">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>إدارة الصلاحيات</h5>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">تعيين الصلاحيات للأدوار</h6>
                <select class="form-select form-select-sm" id="role-select" style="width: 200px;">
                    <option value="manager">مدير</option>
                    <option value="cashier">كاشير</option>
                    <option value="employee">موظف</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <form id="permissions-form">
                <div class="row" id="permissions-list">
                    <!-- سيتم تحميل البيانات هنا عبر AJAX -->
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary" id="save-permissions-btn">
                        <i class="fas fa-save me-1"></i> حفظ الصلاحيات
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<!-- قالب إعدادات الفواتير -->
<template id="invoice-settings-template">
    <form id="invoice-settings-form">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">محتوى الفاتورة</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="invoice-header" class="form-label">ترويسة الفاتورة</label>
                            <textarea class="form-control" id="invoice-header" name="invoice_header" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="invoice-footer" class="form-label">تذييل الفاتورة</label>
                            <textarea class="form-control" id="invoice-footer" name="invoice_footer" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="invoice-notes" class="form-label">ملاحظات افتراضية</label>
                            <textarea class="form-control" id="invoice-notes" name="invoice_notes" rows="2"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="show-tax" name="show_tax">
                            <label class="form-check-label" for="show-tax">
                                إظهار الضريبة في الفاتورة
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="show-employee-name" name="show_employee_name">
                            <label class="form-check-label" for="show-employee-name">
                                إظهار اسم الموظف في الفاتورة
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">إعدادات الطباعة</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="receipt-width" class="form-label">عرض الإيصال (ملم)</label>
                            <select class="form-select" id="receipt-width" name="receipt_width">
                                <option value="58">58 ملم</option>
                                <option value="80">80 ملم</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="font-size" class="form-label">حجم الخط</label>
                            <select class="form-select" id="font-size" name="font_size">
                                <option value="small">صغير</option>
                                <option value="medium">متوسط</option>
                                <option value="large">كبير</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="auto-print" name="auto_print">
                            <label class="form-check-label" for="auto-print">
                                طباعة تلقائية عند إنشاء فاتورة
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="print-logo" name="print_logo">
                            <label class="form-check-label" for="print-logo">
                                طباعة الشعار على الفاتورة
                            </label>
                        </div>
                        <div class="mb-3">
                            <label for="print-copies" class="form-label">عدد النسخ</label>
                            <input type="number" class="form-control" id="print-copies" name="print_copies" min="1" max="5" value="1">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-secondary me-2" id="test-print-btn">
                <i class="fas fa-print me-1"></i> اختبار الطباعة
            </button>
            <button type="submit" class="btn btn-primary" id="save-invoice-settings">
                <i class="fas fa-save me-1"></i> حفظ الإعدادات
            </button>
        </div>
    </form>
</template>

<!-- قالب النسخ الاحتياطي -->
<template id="backup-settings-template">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">النسخ الاحتياطي</h6>
                </div>
                <div class="card-body">
                    <p>يمكنك إنشاء نسخة احتياطية لقاعدة البيانات أو استعادة نسخة محفوظة سابقًا.</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" id="create-backup-btn">
                            <i class="fas fa-download me-1"></i> إنشاء نسخة احتياطية
                        </button>
                    </div>
                    <hr>
                    <h6>النسخ الاحتياطية السابقة</h6>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-sm" id="backups-table">
                            <thead>
                                <tr>
                                    <th>اسم الملف</th>
                                    <th>التاريخ</th>
                                    <th>الحجم</th>
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
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">استعادة النسخة الاحتياطية</h6>
                </div>
                <div class="card-body">
                    <p class="text-danger">تحذير: استعادة النسخة الاحتياطية سيؤدي إلى استبدال جميع البيانات الحالية.</p>
                    <form id="restore-backup-form">
                        <div class="mb-3">
                            <label for="backup-file" class="form-label">ملف النسخة الاحتياطية</label>
                            <input type="file" class="form-control" id="backup-file" name="backup_file" accept=".sql">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning" id="restore-backup-btn">
                                <i class="fas fa-upload me-1"></i> استعادة النسخة الاحتياطية
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">الجدولة التلقائية</h6>
                </div>
                <div class="card-body">
                    <form id="auto-backup-form">
                        <div class="mb-3">
                            <label for="auto-backup-frequency" class="form-label">تكرار النسخ الاحتياطي</label>
                            <select class="form-select" id="auto-backup-frequency" name="auto_backup_frequency">
                                <option value="disabled">غير مفعل</option>
                                <option value="daily">يومي</option>
                                <option value="weekly">أسبوعي</option>
                                <option value="monthly">شهري</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="auto-backup-time" class="form-label">وقت النسخ الاحتياطي</label>
                            <input type="time" class="form-control" id="auto-backup-time" name="auto_backup_time">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="auto-delete-old" name="auto_delete_old">
                            <label class="form-check-label" for="auto-delete-old">
                                حذف النسخ القديمة تلقائيًا (أقدم من 30 يوم)
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="save-auto-backup-settings">
                                <i class="fas fa-save me-1"></i> حفظ إعدادات الجدولة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Modal إضافة/تعديل مستخدم -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">إضافة مستخدم جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="user-form">
                    <input type="hidden" id="user-id">
                    <div class="mb-3">
                        <label for="username" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="full-name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full-name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">رقم الهاتف</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور <span class="text-danger password-required">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">الدور <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">اختر الدور</option>
                            <option value="admin">مدير النظام</option>
                            <option value="manager">مدير</option>
                            <option value="cashier">كاشير</option>
                            <option value="employee">موظف</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="branch-id" class="form-label">الفرع</label>
                        <select class="form-select" id="branch-id" name="branch_id">
                            <option value="">اختر الفرع</option>
                            <!-- سيتم تحميل الفروع بواسطة AJAX -->
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="active" name="active" checked>
                        <label class="form-check-label" for="active">
                            نشط
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-user-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // تحميل المحتوى الافتراضي (الإعدادات العامة)
        loadSettingsContent('general');
        
        // استماع أحداث النقر على علامات التبويب
        $('.nav-link').on('click', function(e) {
            e.preventDefault();
            const action = $(this).data('action');
            
            // تنشيط التبويب المحدد
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            
            // تحميل المحتوى المناسب
            loadSettingsContent(action);
        });
        
        // دالة لتحميل محتوى الإعدادات
        function loadSettingsContent(action) {
            // إظهار مؤشر التحميل
            $('#settings-content').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">جاري التحميل...</span></div></div>');
            
            switch (action) {
                case 'general':
                    loadGeneralSettings();
                    break;
                case 'users':
                    loadUsersSettings();
                    break;
                case 'permissions':
                    loadPermissionsSettings();
                    break;
                case 'invoice':
                    loadInvoiceSettings();
                    break;
                case 'backup':
                    loadBackupSettings();
                    break;
                default:
                    loadGeneralSettings();
            }
        }
        
        //=============== الإعدادات العامة =================//
        function loadGeneralSettings() {
            // إضافة قالب الإعدادات العامة
            const template = document.getElementById('general-settings-template');
            $('#settings-content').html(template.innerHTML);
            
            // تحميل الإعدادات الحالية
            $.ajax({
                url: 'api/settings/get.php',
                type: 'GET',
                data: { category: 'general' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // تعبئة النموذج بالقيم المسترجعة
                        const settings = response.data;
                        $('#salon-name').val(settings.salon_name);
                        $('#salon-phone').val(settings.salon_phone);
                        $('#salon-email').val(settings.salon_email);
                        $('#salon-address').val(settings.salon_address);
                        $('#currency').val(settings.currency);
                        $('#tax-rate').val(settings.tax_rate);
                        $('#appointment-duration').val(settings.appointment_duration);
                        $('#working-hours-start').val(settings.working_hours_start);
                        $('#working-hours-end').val(settings.working_hours_end);
                        $('#enable-online-booking').prop('checked', settings.enable_online_booking === '1');
                        
                        // عرض الشعار إذا كان موجودًا
                        if (settings.salon_logo) {
                            $('#logo-preview').html(`<img src="${settings.salon_logo}" alt="Salon Logo" class="img-thumbnail" style="max-height: 100px;">`);
                        }
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء تحميل الإعدادات', 'خطأ', false);
                    console.error(error);
                }
            });
            
            // حدث رفع الشعار
            $('#salon-logo').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // عرض معاينة الصورة
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#logo-preview').html(`<img src="${e.target.result}" alt="Logo Preview" class="img-thumbnail" style="max-height: 100px;">`);
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // حدث حفظ الإعدادات العامة
            $('#general-settings-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                // تعطيل زر الحفظ أثناء المعالجة
                $('#save-general-settings').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
                
                $.ajax({
                    url: 'api/settings/save.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert('تم حفظ الإعدادات بنجاح', 'نجاح', false);
                        } else {
                            showAlert(response.message, 'خطأ', false);
                        }
                        
                        // إعادة تفعيل زر الحفظ
                        $('#save-general-settings').prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ الإعدادات');
                    },
                    error: function(xhr, status, error) {
                        showAlert('حدث خطأ أثناء حفظ الإعدادات', 'خطأ', false);
                        console.error(error);
                        
                        // إعادة تفعيل زر الحفظ
                        $('#save-general-settings').prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ الإعدادات');
                    }
                });
            });
        }
        //////////////////////

        
        //=============== إعدادات المستخدمين =================//
        function loadUsersSettings() {
            // إضافة قالب إدارة المستخدمين
            const template = document.getElementById('users-settings-template');
            $('#settings-content').html(template.innerHTML);
            
            // تحميل المستخدمين
            loadUsers();
            
            // تحميل الفروع للاختيار
            loadBranches();
            
            // زر إضافة مستخدم جديد
            $('#add-user-btn').on('click', function() {
                // إعادة تعيين النموذج
                $('#user-form')[0].reset();
                $('#user-id').val('');
                
                // إظهار حقل كلمة المرور كمطلوب
                $('.password-required').show();
                $('#password').prop('required', true);
                
                // تغيير عنوان النافذة المنبثقة
                $('#userModalLabel').text('إضافة مستخدم جديد');
                
                // عرض النافذة المنبثقة
                $('#userModal').modal('show');
            });
            
            // حدث حفظ المستخدم
            $('#save-user-btn').on('click', function() {
                saveUser();
            });
            
            // زر إظهار/إخفاء كلمة المرور
            $('.toggle-password').on('click', function() {
                const passwordField = $('#password');
                const passwordType = passwordField.attr('type');
                
                if (passwordType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        }
        
        function loadUsers() {
            // عرض مؤشر التحميل
            $('#users-table tbody').html('<tr><td colspan="8" class="text-center">جاري التحميل...</td></tr>');
            
            $.ajax({
                url: 'api/users/list.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderUsersTable(response.data);
                    } else {
                        $('#users-table tbody').html('<tr><td colspan="8" class="text-center text-danger">حدث خطأ أثناء تحميل المستخدمين</td></tr>');
                        console.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#users-table tbody').html('<tr><td colspan="8" class="text-center text-danger">حدث خطأ أثناء تحميل المستخدمين</td></tr>');
                    console.error(error);
                }
            });
        }
        
        function renderUsersTable(users) {
            if (users.length === 0) {
                $('#users-table tbody').html('<tr><td colspan="8" class="text-center">لا يوجد مستخدمين</td></tr>');
                return;
            }
            
            let html = '';
            
            users.forEach(function(user) {
                // تنسيق البيانات
                const roleName = getRoleName(user.role);
                const status = user.active == 1 
                    ? '<span class="badge bg-success">نشط</span>' 
                    : '<span class="badge bg-secondary">غير نشط</span>';
                
                const lastLogin = user.last_login ? new Date(user.last_login).toLocaleString('ar-SA') : '-';
                
                html += `
                    <tr>
                        <td>${user.username}</td>
                        <td>${user.full_name}</td>
                        <td>${user.email || '-'}</td>
                        <td>${roleName}</td>
                        <td>${user.branch_name || '-'}</td>
                        <td>${status}</td>
                        <td>${lastLogin}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-user" data-id="${user.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-user" data-id="${user.id}" data-name="${user.username}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            $('#users-table tbody').html(html);
            
            // إضافة مستمعات الأحداث للأزرار
            $('.edit-user').on('click', function() {
                const userId = $(this).data('id');
                editUser(userId);
            });
            
            $('.delete-user').on('click', function() {
                const userId = $(this).data('id');
                const userName = $(this).data('name');
                deleteUser(userId, userName);
            });
        }
        
        function loadBranches() {
            $.ajax({
                url: 'api/branches/list.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">اختر الفرع</option>';
                        
                        response.data.forEach(function(branch) {
                            options += `<option value="${branch.id}">${branch.name}</option>`;
                        });
                        
                        $('#branch-id').html(options);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('خطأ في تحميل الفروع:', error);
                }
            });
        }
        
        function getRoleName(role) {
            const roles = {
                'admin': 'مدير النظام',
                'manager': 'مدير',
                'cashier': 'كاشير',
                'employee': 'موظف'
            };
            
            return roles[role] || role;
        }
        
        function editUser(userId) {
            $.ajax({
                url: 'api/users/get.php',
                type: 'GET',
                data: { id: userId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const user = response.data;
                        
                        // تعبئة النموذج
                        $('#user-id').val(user.id);
                        $('#username').val(user.username);
                        $('#full-name').val(user.full_name);
                        $('#email').val(user.email);
                        $('#phone').val(user.phone);
                        $('#password').val('').prop('required', false);
                        $('.password-required').hide();
                        $('#role').val(user.role);
                        $('#branch-id').val(user.branch_id);
                        $('#active').prop('checked', user.active == 1);
                        
                        // تغيير عنوان النافذة المنبثقة
                        $('#userModalLabel').text('تعديل المستخدم');
                        
                        // عرض النافذة المنبثقة
                        $('#userModal').modal('show');
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء تحميل بيانات المستخدم', 'خطأ', false);
                    console.error(error);
                }
            });
        }
        
        function saveUser() {
            // التحقق من صحة النموذج
            if (!$('#user-form')[0].checkValidity()) {
                $('#user-form')[0].reportValidity();
                return;
            }
            
            // تجميع بيانات النموذج
            const userId = $('#user-id').val();
            const userData = {
                username: $('#username').val(),
                full_name: $('#full-name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                role: $('#role').val(),
                branch_id: $('#branch-id').val(),
                active: $('#active').is(':checked') ? 1 : 0
            };
            
            // إضافة كلمة المرور إذا تم إدخالها
            const password = $('#password').val();
            if (password) {
                userData.password = password;
            }
            
            // تحديد URL حسب نوع العملية (إضافة/تعديل)
            const url = userId ? 'api/users/update.php' : 'api/users/create.php';
            
            // إضافة معرف المستخدم في حالة التعديل
            if (userId) {
                userData.id = userId;
            }
            
            // تعطيل زر الحفظ أثناء المعالجة
            $('#save-user-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
            
            $.ajax({
                url: url,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(userData),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // إغلاق النافذة المنبثقة
                        $('#userModal').modal('hide');
                        
                        // إعادة تحميل المستخدمين
                        loadUsers();
                        
                        // عرض رسالة نجاح
                        showAlert(userId ? 'تم تعديل المستخدم بنجاح' : 'تم إضافة المستخدم بنجاح', 'نجاح', false);
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-user-btn').prop('disabled', false).text('حفظ');
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء حفظ المستخدم', 'خطأ', false);
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-user-btn').prop('disabled', false).text('حفظ');
                }
            });
        }
        
        function deleteUser(userId, userName) {
            showAlert(
                `هل أنت متأكد من حذف المستخدم "${userName}"؟`,
                'تأكيد الحذف',
                true,
                function() {
                    $.ajax({
                        url: 'api/users/delete.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ id: userId }),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                // إعادة تحميل المستخدمين
                                loadUsers();
                                
                                // عرض رسالة نجاح
                                showAlert('تم حذف المستخدم بنجاح', 'نجاح', false);
                            } else {
                                showAlert(response.message, 'خطأ', false);
                            }
                        },
                        error: function(xhr, status, error) {
                            showAlert('حدث خطأ أثناء حذف المستخدم', 'خطأ', false);
                            console.error(error);
                        }
                    });
                }
            );
        }
        
        //=============== إعدادات الصلاحيات =================//
        function loadPermissionsSettings() {
            // إضافة قالب إدارة الصلاحيات
            const template = document.getElementById('permissions-settings-template');
            $('#settings-content').html(template.innerHTML);
            
            // تحميل الصلاحيات للدور المحدد
            loadRolePermissions($('#role-select').val());
            
            // حدث تغيير الدور
            $('#role-select').on('change', function() {
                loadRolePermissions($(this).val());
            });
            
            // حدث حفظ الصلاحيات
            $('#permissions-form').on('submit', function(e) {
                e.preventDefault();
                saveRolePermissions();
            });
        }
        
        function loadRolePermissions(role) {
            // عرض مؤشر التحميل
            $('#permissions-list').html('<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">جاري التحميل...</span></div></div>');
            
            $.ajax({
                url: 'api/permissions/get_role_permissions.php',
                type: 'GET',
                data: { role: role },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderPermissionsList(response.data);
                    } else {
                        $('#permissions-list').html('<div class="col-12 text-danger">حدث خطأ أثناء تحميل الصلاحيات</div>');
                        console.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#permissions-list').html('<div class="col-12 text-danger">حدث خطأ أثناء تحميل الصلاحيات</div>');
                    console.error(error);
                }
            });
        }
        
        function renderPermissionsList(permissionsData) {
            const permissions = permissionsData.permissions;
            const rolePermissions = permissionsData.role_permissions;
            
            if (permissions.length === 0) {
                $('#permissions-list').html('<div class="col-12">لا توجد صلاحيات متاحة</div>');
                return;
            }
            
            // تصنيف الصلاحيات حسب الفئة
            const categories = {};
            
            permissions.forEach(function(permission) {
                const categoryName = permission.category || 'أخرى';
                
                if (!categories[categoryName]) {
                    categories[categoryName] = [];
                }
                
                categories[categoryName].push(permission);
            });
            
            let html = '';
            
            // إنشاء قوائم الصلاحيات حسب الفئة
            for (const category in categories) {
                html += `
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">${category}</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input select-all-category" type="checkbox" id="select-all-${category.replace(/\s+/g, '-')}">
                                        <label class="form-check-label" for="select-all-${category.replace(/\s+/g, '-')}">
                                            <strong>تحديد الكل</strong>
                                        </label>
                                    </div>
                                </div>
                                <hr>
                `;
                
                categories[category].forEach(function(permission) {
                    const isChecked = rolePermissions.includes(permission.id);
                    
                    html += `
                        <div class="form-check mb-2">
                            <input class="form-check-input permission-checkbox" type="checkbox" id="permission-${permission.id}" name="permissions[]" value="${permission.id}" ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label" for="permission-${permission.id}">
                                ${permission.description || permission.name}
                            </label>
                        </div>
                    `;
                });
                
                html += `
                            </div>
                        </div>
                    </div>
                `;
            }
            
            $('#permissions-list').html(html);
            
            // إضافة حدث تحديد الكل لكل فئة
            $('.select-all-category').on('change', function() {
                const isChecked = $(this).is(':checked');
                $(this).closest('.card').find('.permission-checkbox').prop('checked', isChecked);
            });
            
            // تحديث حالة "تحديد الكل" بناءً على الصلاحيات المحددة
            $('.card').each(function() {
                const totalCheckboxes = $(this).find('.permission-checkbox').length;
                const checkedCheckboxes = $(this).find('.permission-checkbox:checked').length;
                
                $(this).find('.select-all-category').prop('checked', totalCheckboxes === checkedCheckboxes);
            });
            
            // إضافة حدث تغيير حالة "تحديد الكل" عند تغيير أي صلاحية
            $('.permission-checkbox').on('change', function() {
                const card = $(this).closest('.card');
                const totalCheckboxes = card.find('.permission-checkbox').length;
                const checkedCheckboxes = card.find('.permission-checkbox:checked').length;
                
                card.find('.select-all-category').prop('checked', totalCheckboxes === checkedCheckboxes);
            });
        }
        
        function saveRolePermissions() {
            // تجميع الصلاحيات المحددة
            const selectedPermissions = [];
            $('input[name="permissions[]"]:checked').each(function() {
                selectedPermissions.push($(this).val());
            });
            
            // تعطيل زر الحفظ أثناء المعالجة
            $('#save-permissions-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
            
            $.ajax({
                url: 'api/permissions/update_role_permissions.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    role: $('#role-select').val(),
                    permissions: selectedPermissions
                }),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('تم حفظ الصلاحيات بنجاح', 'نجاح', false);
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-permissions-btn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ الصلاحيات');
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء حفظ الصلاحيات', 'خطأ', false);
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-permissions-btn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ الصلاحيات');
                }
            });
        }
        
        //=============== إعدادات الفواتير =================//
        function loadInvoiceSettings() {
            // إضافة قالب إعدادات الفواتير
            const template = document.getElementById('invoice-settings-template');
            $('#settings-content').html(template.innerHTML);
            
            // تحميل الإعدادات الحالية
            $.ajax({
                url: 'api/settings/get.php',
                type: 'GET',
                data: { category: 'invoice' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // تعبئة النموذج بالقيم المسترجعة
                        const settings = response.data;
                        $('#invoice-header').val(settings.invoice_header);
                        $('#invoice-footer').val(settings.invoice_footer);
                        $('#invoice-notes').val(settings.invoice_notes);
                        $('#show-tax').prop('checked', settings.show_tax === '1');
                        $('#show-employee-name').prop('checked', settings.show_employee_name === '1');
                        $('#receipt-width').val(settings.receipt_width);
                        $('#font-size').val(settings.font_size);
                        $('#auto-print').prop('checked', settings.auto_print === '1');
                        $('#print-logo').prop('checked', settings.print_logo === '1');
                        $('#print-copies').val(settings.print_copies);
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء تحميل الإعدادات', 'خطأ', false);
                    console.error(error);
                }
            });
            
            // حدث حفظ إعدادات الفواتير
            $('#invoice-settings-form').on('submit', function(e) {
                e.preventDefault();
                
                // تجميع بيانات النموذج
                const formData = {
                    invoice_header: $('#invoice-header').val(),
                    invoice_footer: $('#invoice-footer').val(),
                    invoice_notes: $('#invoice-notes').val(),
                    show_tax: $('#show-tax').is(':checked') ? '1' : '0',
                    show_employee_name: $('#show-employee-name').is(':checked') ? '1' : '0',
                    receipt_width: $('#receipt-width').val(),
                    font_size: $('#font-size').val(),
                    auto_print: $('#auto-print').is(':checked') ? '1' : '0',
                    print_logo: $('#print-logo').is(':checked') ? '1' : '0',
                    print_copies: $('#print-copies').val(),
                    category: 'invoice'
                };
                
                // تعطيل زر الحفظ أثناء المعالجة
                $('#save-invoice-settings').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
                
                $.ajax({
                    url: 'api/settings/save.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert('تم حفظ إعدادات الفواتير بنجاح', 'نجاح', false);
                        } else {
                            showAlert(response.message, 'خطأ', false);
                        }
                        
                        // إعادة تفعيل زر الحفظ
                        $('#save-invoice-settings').prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ الإعدادات');
                    },
                    error: function(xhr, status, error) {
                        showAlert('حدث خطأ أثناء حفظ الإعدادات', 'خطأ', false);
                        console.error(error);
                        
                        // إعادة تفعيل زر الحفظ
                        $('#save-invoice-settings').prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ الإعدادات');
                    }
                });
            });
            
            // زر اختبار الطباعة
            $('#test-print-btn').on('click', function() {
                window.open('api/invoices/test_print.php', '_blank');
            });
        }
        
        //=============== إعدادات النسخ الاحتياطي =================//
        function loadBackupSettings() {
            // إضافة قالب إعدادات النسخ الاحتياطي
            const template = document.getElementById('backup-settings-template');
            $('#settings-content').html(template.innerHTML);
            
            // تحميل النسخ الاحتياطية السابقة
            loadBackups();
            
            // تحميل إعدادات النسخ الاحتياطي التلقائي
            loadAutoBackupSettings();
            
            // زر إنشاء نسخة احتياطية
            $('#create-backup-btn').on('click', function() {
                createBackup();
            });
            
            // حدث استعادة نسخة احتياطية
            $('#restore-backup-form').on('submit', function(e) {
                e.preventDefault();
                
                if (!$('#backup-file').val()) {
                    showAlert('يرجى اختيار ملف النسخة الاحتياطية', 'تنبيه', false);
                    return;
                }
                
                showAlert(
                    'تحذير: استعادة النسخة الاحتياطية سيؤدي إلى استبدال جميع البيانات الحالية. هل أنت متأكد من المتابعة؟',
                    'تأكيد الاستعادة',
                    true,
                    function() {
                        const formData = new FormData($('#restore-backup-form')[0]);
                        restoreBackup(formData);
                    }
                );
            });
            
            // حدث حفظ إعدادات النسخ الاحتياطي التلقائي
            $('#auto-backup-form').on('submit', function(e) {
                e.preventDefault();
                saveAutoBackupSettings();
            });
        }
        
        function loadBackups() {
            // عرض مؤشر التحميل
            $('#backups-table tbody').html('<tr><td colspan="4" class="text-center">جاري التحميل...</td></tr>');
            
            $.ajax({
                url: 'api/backup/list.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderBackupsTable(response.data);
                    } else {
                        $('#backups-table tbody').html('<tr><td colspan="4" class="text-center text-danger">حدث خطأ أثناء تحميل النسخ الاحتياطية</td></tr>');
                        console.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#backups-table tbody').html('<tr><td colspan="4" class="text-center text-danger">حدث خطأ أثناء تحميل النسخ الاحتياطية</td></tr>');
                    console.error(error);
                }
            });
        }
        
        function renderBackupsTable(backups) {
            if (backups.length === 0) {
                $('#backups-table tbody').html('<tr><td colspan="4" class="text-center">لا توجد نسخ احتياطية</td></tr>');
                return;
            }
            
            let html = '';
            
            backups.forEach(function(backup) {
                html += `
                    <tr>
                        <td>${backup.filename}</td>
                        <td>${backup.date}</td>
                        <td>${backup.size}</td>
                        <td>
                            <a href="api/backup/download.php?file=${backup.filename}" class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i>
                            </a>
                            <button class="btn btn-sm btn-success restore-backup" data-file="${backup.filename}">
                                <i class="fas fa-undo-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-backup" data-file="${backup.filename}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            $('#backups-table tbody').html(html);
            
            // إضافة مستمعات الأحداث للأزرار
            $('.restore-backup').on('click', function() {
                const filename = $(this).data('file');
                showAlert(
                    'تحذير: استعادة النسخة الاحتياطية سيؤدي إلى استبدال جميع البيانات الحالية. هل أنت متأكد من المتابعة؟',
                    'تأكيد الاستعادة',
                    true,
                    function() {
                        restoreBackupFile(filename);
                    }
                );
            });
            
            $('.delete-backup').on('click', function() {
                const filename = $(this).data('file');
                deleteBackup(filename);
            });
        }
        
        function loadAutoBackupSettings() {
            $.ajax({
                url: 'api/settings/get.php',
                type: 'GET',
                data: { category: 'backup' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // تعبئة النموذج بالقيم المسترجعة
                        const settings = response.data;
                        $('#auto-backup-frequency').val(settings.auto_backup_frequency || 'disabled');
                        $('#auto-backup-time').val(settings.auto_backup_time || '00:00');
                        $('#auto-delete-old').prop('checked', settings.auto_delete_old === '1');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('خطأ في تحميل إعدادات النسخ الاحتياطي التلقائي:', error);
                }
            });
        }
        
        function createBackup() {
            // تعطيل زر الإنشاء أثناء المعالجة
            $('#create-backup-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري إنشاء النسخة الاحتياطية...');
            
            $.ajax({
                url: 'api/backup/create.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('تم إنشاء النسخة الاحتياطية بنجاح', 'نجاح', false);
                        
                        // إعادة تحميل قائمة النسخ الاحتياطية
                        loadBackups();
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                    
                    // إعادة تفعيل زر الإنشاء
                    $('#create-backup-btn').prop('disabled', false).html('<i class="fas fa-download me-1"></i> إنشاء نسخة احتياطية');
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء إنشاء النسخة الاحتياطية', 'خطأ', false);
                    console.error(error);
                    
                    // إعادة تفعيل زر الإنشاء
                    $('#create-backup-btn').prop('disabled', false).html('<i class="fas fa-download me-1"></i> إنشاء نسخة احتياطية');
                }
            });
        }
        
        function restoreBackupFile(filename) {
            $.ajax({
                url: 'api/backup/restore.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ filename: filename }),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('تم استعادة النسخة الاحتياطية بنجاح. سيتم إعادة تحميل الصفحة.', 'نجاح', false);
                        
                        // إعادة تحميل الصفحة بعد الاستعادة
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء استعادة النسخة الاحتياطية', 'خطأ', false);
                    console.error(error);
                }
            });
        }
        
        function restoreBackup(formData) {
            $.ajax({
                url: 'api/backup/restore_upload.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('تم استعادة النسخة الاحتياطية بنجاح. سيتم إعادة تحميل الصفحة.', 'نجاح', false);
                        
                        // إعادة تحميل الصفحة بعد الاستعادة
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء استعادة النسخة الاحتياطية', 'خطأ', false);
                    console.error(error);
                }
            });
        }
        
        function deleteBackup(filename) {
            showAlert(
                `هل أنت متأكد من حذف النسخة الاحتياطية "${filename}"؟`,
                'تأكيد الحذف',
                true,
                function() {
                    $.ajax({
                        url: 'api/backup/delete.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ filename: filename }),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                showAlert('تم حذف النسخة الاحتياطية بنجاح', 'نجاح', false);
                                
                                // إعادة تحميل قائمة النسخ الاحتياطية
                                loadBackups();
                            } else {
                                showAlert(response.message, 'خطأ', false);
                            }
                        },
                        error: function(xhr, status, error) {
                            showAlert('حدث خطأ أثناء حذف النسخة الاحتياطية', 'خطأ', false);
                            console.error(error);
                        }
                    });
                }
            );
        }
        
        function saveAutoBackupSettings() {
            // تجميع بيانات النموذج
            const formData = {
                auto_backup_frequency: $('#auto-backup-frequency').val(),
                auto_backup_time: $('#auto-backup-time').val(),
                auto_delete_old: $('#auto-delete-old').is(':checked') ? '1' : '0',
                category: 'backup'
            };
            
            // تعطيل زر الحفظ أثناء المعالجة
            $('#save-auto-backup-settings').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
            
            $.ajax({
                url: 'api/settings/save.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('تم حفظ إعدادات النسخ الاحتياطي التلقائي بنجاح', 'نجاح', false);
                    } else {
                        showAlert(response.message, 'خطأ', false);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-auto-backup-settings').prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ إعدادات الجدولة');
                },
                error: function(xhr, status, error) {
                    showAlert('حدث خطأ أثناء حفظ الإعدادات', 'خطأ', false);
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-auto-backup-settings').prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ إعدادات الجدولة');
                }
            });
        }
    });
</script>