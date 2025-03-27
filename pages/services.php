<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-cut"></i> إدارة الخدمات</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" id="add-service-btn">
            <i class="fas fa-plus"></i> إضافة خدمة جديدة
        </button>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold">قائمة الخدمات</h6>
        <div class="d-flex">
            <div class="input-group input-group-sm me-2" style="width: 200px;">
                <input type="text" class="form-control" id="search-service" placeholder="بحث...">
                <button class="btn btn-outline-secondary" type="button" id="clear-search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <select class="form-select form-select-sm me-2" id="category-filter" style="width: 150px;">
                <option value="">جميع الفئات</option>
                <!-- سيتم تحميل الفئات عبر AJAX -->
            </select>
            <select class="form-select form-select-sm me-2" id="status-filter" style="width: 120px;">
                <option value="">جميع الحالات</option>
                <option value="1">نشط</option>
                <option value="0">غير نشط</option>
            </select>
            <button class="btn btn-outline-secondary btn-sm" id="export-services">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="services-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>اسم الخدمة</th>
                        <th>الفئة</th>
                        <th>السعر</th>
                        <th>المدة (دقيقة)</th>
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

<!-- Modal إضافة/تعديل خدمة -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">إضافة خدمة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="service-form">
                    <input type="hidden" id="service-id">
                    <div class="mb-3">
                        <label for="service-name" class="form-label">اسم الخدمة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="service-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="service-category" class="form-label">الفئة</label>
                        <input type="text" class="form-control" id="service-category" name="category" list="categories-list">
                        <datalist id="categories-list">
                            <!-- سيتم تحميل الفئات بواسطة AJAX -->
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label for="service-price" class="form-label">السعر <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="service-price" name="price" step="0.01" min="0" required>
                            <span class="input-group-text">ر.س</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="service-duration" class="form-label">المدة (بالدقائق)</label>
                        <input type="number" class="form-control" id="service-duration" name="duration" min="5" value="30">
                    </div>
                    <div class="mb-3">
                        <label for="service-description" class="form-label">وصف الخدمة</label>
                        <textarea class="form-control" id="service-description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="service-active" name="active" checked>
                        <label class="form-check-label" for="service-active">
                            نشط
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-service-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal تفاصيل الخدمة -->
<div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-labelledby="serviceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceDetailsModalLabel">تفاصيل الخدمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">معلومات الخدمة</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>اسم الخدمة:</strong> <span id="service-details-name"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>الفئة:</strong> <span id="service-details-category"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>السعر:</strong> <span id="service-details-price"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>المدة (بالدقائق):</strong> <span id="service-details-duration"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>الحالة:</strong> <span id="service-details-status"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>وصف الخدمة:</strong> <p id="service-details-description"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">إحصائيات الخدمة</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>عدد مرات الاستخدام:</strong> <span id="service-details-usage"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>إجمالي العائدات:</strong> <span id="service-details-revenue"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>آخر استخدام:</strong> <span id="service-details-last-usage"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">الموظفون المختصون</h6>
                            </div>
                            <div class="card-body" style="max-height: 150px; overflow-y: auto;">
                                <ul class="list-group list-group-flush" id="service-employees">
                                    <!-- سيتم تحميل البيانات هنا عبر AJAX -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'): ?>
                <button type="button" class="btn btn-primary" id="edit-service-btn">
                    <i class="fas fa-edit"></i> تعديل
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // تحميل الخدمات عند تحميل الصفحة
        loadServices();
        
        // تحميل الفئات
        loadCategories();
        
        // أحداث البحث والتصفية
        $('#search-service').on('input', function() {
            filterServices();
        });
        
        $('#category-filter, #status-filter').on('change', function() {
            filterServices();
        });
        
        $('#clear-search').on('click', function() {
            $('#search-service').val('');
            filterServices();
        });
        
        // زر إضافة خدمة جديدة
        $('#add-service-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#service-form')[0].reset();
            $('#service-id').val('');
            
            // تعيين القيم الافتراضية
            $('#service-active').prop('checked', true);
            $('#service-duration').val(30);
            
            // تغيير عنوان النافذة المنبثقة
            $('#serviceModalLabel').text('إضافة خدمة جديدة');
            
            // عرض النافذة المنبثقة
            $('#serviceModal').modal('show');
        });
        
        // زر حفظ الخدمة
        $('#save-service-btn').on('click', function() {
            saveService();
        });
        
        // زر تعديل في نافذة التفاصيل
        $('#edit-service-btn').on('click', function() {
            const serviceId = $(this).data('id');
            $('#serviceDetailsModal').modal('hide');
            editService(serviceId);
        });
        
        // زر تصدير الخدمات
        $('#export-services').on('click', function() {
            window.location.href = 'api/services/export.php';
        });
    });
    
    // دالة لتحميل قائمة الخدمات
    function loadServices() {
        // عرض مؤشر التحميل
        $('#services-table tbody').html('<tr><td colspan="6" class="text-center">جاري التحميل...</td></tr>');
        
        // طلب قائمة الخدمات من API
        $.ajax({
            url: 'api/services/list.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    renderServicesTable(response.data);
                } else {
                    $('#services-table tbody').html('<tr><td colspan="6" class="text-center text-danger">حدث خطأ أثناء تحميل الخدمات</td></tr>');
                    console.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                $('#services-table tbody').html('<tr><td colspan="6" class="text-center text-danger">حدث خطأ أثناء تحميل الخدمات</td></tr>');
                console.error(error);
            }
        });
    }
    
    // دالة لعرض جدول الخدمات
    function renderServicesTable(services) {
        if(services.length === 0) {
            $('#services-table tbody').html('<tr><td colspan="6" class="text-center">لا توجد خدمات</td></tr>');
            return;
        }
        
        let html = '';
        
        services.forEach(function(service) {
            // تنسيق البيانات
            const status = service.active == 1 
                ? '<span class="badge bg-success">نشط</span>' 
                : '<span class="badge bg-secondary">غير نشط</span>';
            
            const price = parseFloat(service.price).toFixed(2) + ' ر.س';
            const category = service.category || '-';
            
            // بناء أزرار الإجراءات بناءً على دور المستخدم
            let actionButtons = `
                <button class="btn btn-sm btn-info view-service" data-id="${service.id}">
                    <i class="fas fa-eye"></i>
                </button>
            `;
            
            // إضافة أزرار التعديل والحذف للمدير والآدمن فقط
            if (['admin', 'manager'].includes('<?php echo $_SESSION['role']; ?>')) {
                actionButtons += `
                    <button class="btn btn-sm btn-primary edit-service" data-id="${service.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-service" data-id="${service.id}" data-name="${service.name}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
            }
            
            html += `
                <tr>
                    <td>${service.name}</td>
                    <td>${category}</td>
                    <td>${price}</td>
                    <td>${service.duration}</td>
                    <td>${status}</td>
                    <td>${actionButtons}</td>
                </tr>
            `;
        });
        
        $('#services-table tbody').html(html);
        
        // إضافة مستمعات الأحداث للأزرار
        $('.view-service').on('click', function() {
            const serviceId = $(this).data('id');
            viewService(serviceId);
        });
        
        $('.edit-service').on('click', function() {
            const serviceId = $(this).data('id');
            editService(serviceId);
        });
        
        $('.delete-service').on('click', function() {
            const serviceId = $(this).data('id');
            const serviceName = $(this).data('name');
            deleteService(serviceId, serviceName);
        });
    }
    
    // دالة لتصفية الخدمات
    function filterServices() {
        const searchText = $('#search-service').val().toLowerCase();
        const category = $('#category-filter').val();
        const status = $('#status-filter').val();
        
        $('#services-table tbody tr').each(function() {
            const name = $(this).find('td:eq(0)').text().toLowerCase();
            const serviceCategory = $(this).find('td:eq(1)').text();
            const serviceStatus = $(this).find('td:eq(4)').text().includes('نشط') ? '1' : '0';
            
            const matchesSearch = name.includes(searchText);
            const matchesCategory = category === '' || serviceCategory === category || (serviceCategory === '-' && category === '');
            const matchesStatus = status === '' || serviceStatus === status;
            
            if(matchesSearch && matchesCategory && matchesStatus) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    // دالة لتحميل الفئات
    function loadCategories() {
        $.ajax({
            url: 'api/services/categories.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    const categories = response.data;
                    
                    // إضافة الفئات إلى قائمة التصفية
                    let filterHtml = '<option value="">جميع الفئات</option>';
                    
                    // إضافة الفئات إلى قائمة datalist
                    let datalistHtml = '';
                    
                    categories.forEach(function(category) {
                        filterHtml += `<option value="${category}">${category}</option>`;
                        datalistHtml += `<option value="${category}">`;
                    });
                    
                    $('#category-filter').html(filterHtml);
                    $('#categories-list').html(datalistHtml);
                }
            },
            error: function(xhr, status, error) {
                console.error('خطأ في تحميل الفئات:', error);
            }
        });
    }
    
    // دالة لعرض تفاصيل خدمة
    function viewService(serviceId) {
        $.ajax({
            url: 'api/services/get.php',
            type: 'GET',
            data: { id: serviceId },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    const service = response.data;
                    
                    // تحديث معلومات الخدمة
                    $('#service-details-name').text(service.name);
                    $('#service-details-category').text(service.category || '-');
                    $('#service-details-price').text(parseFloat(service.price).toFixed(2) + ' ر.س');
                    $('#service-details-duration').text(service.duration);
                    $('#service-details-status').html(
                        service.active == 1 
                        ? '<span class="badge bg-success">نشط</span>' 
                        : '<span class="badge bg-secondary">غير نشط</span>'
                    );
                    $('#service-details-description').text(service.description || '-');
                    
                    // تخزين معرف الخدمة في زر التعديل
                    $('#edit-service-btn').data('id', serviceId);
                    
                    // تحميل المعلومات الإضافية
                    loadServiceStatistics(serviceId);
                    loadServiceEmployees(serviceId);
                    
                    // عرض النافذة المنبثقة
                    $('#serviceDetailsModal').modal('show');
                } else {
                    showAlert(response.message, 'خطأ', false);
                }
            },
            error: function(xhr, status, error) {
                showAlert('حدث خطأ أثناء تحميل بيانات الخدمة', 'خطأ', false);
                console.error(error);
            }
        });
    }
    
    // دالة لتحميل إحصائيات الخدمة
    function loadServiceStatistics(serviceId) {
        $.ajax({
            url: 'api/services/statistics.php',
            type: 'GET',
            data: { id: serviceId },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    const stats = response.data;
                    
                    $('#service-details-usage').text(stats.usage_count || '0');
                    $('#service-details-revenue').text((stats.revenue || '0.00') + ' ر.س');
                    $('#service-details-last-usage').text(stats.last_usage || '-');
                } else {
                    $('#service-details-usage').text('0');
                    $('#service-details-revenue').text('0.00 ر.س');
                    $('#service-details-last-usage').text('-');
                }
            },
            error: function(xhr, status, error) {
                $('#service-details-usage').text('0');
                $('#service-details-revenue').text('0.00 ر.س');
                $('#service-details-last-usage').text('-');
                console.error(error);
            }
        });
    }
    
    // دالة لتحميل الموظفين المختصين بالخدمة
    function loadServiceEmployees(serviceId) {
        $.ajax({
            url: 'api/services/employees.php',
            type: 'GET',
            data: { id: serviceId },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    const employees = response.data;
                    
                    if(employees.length > 0) {
                        let html = '';
                        
                        employees.forEach(function(employee) {
                            html += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${employee.full_name}
                                    <span class="badge bg-primary rounded-pill">${employee.role_name}</span>
                                </li>
                            `;
                        });
                        
                        $('#service-employees').html(html);
                    } else {
                        $('#service-employees').html('<li class="list-group-item">لا يوجد موظفين مختصين بهذه الخدمة</li>');
                    }
                } else {
                    $('#service-employees').html('<li class="list-group-item text-danger">حدث خطأ أثناء تحميل الموظفين</li>');
                }
            },
            error: function(xhr, status, error) {
                $('#service-employees').html('<li class="list-group-item text-danger">حدث خطأ أثناء تحميل الموظفين</li>');
                console.error(error);
            }
        });
    }
    
    // دالة لتعديل خدمة
    function editService(serviceId) {
        $.ajax({
            url: 'api/services/get.php',
            type: 'GET',
            data: { id: serviceId },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    const service = response.data;
                    
                    // تعبئة النموذج ببيانات الخدمة
                    $('#service-id').val(service.id);
                    $('#service-name').val(service.name);
                    $('#service-category').val(service.category);
                    $('#service-price').val(service.price);
                    $('#service-duration').val(service.duration);
                    $('#service-description').val(service.description);
                    $('#service-active').prop('checked', service.active == 1);
                    
                    // تغيير عنوان النافذة المنبثقة
                    $('#serviceModalLabel').text('تعديل الخدمة');
                    
                    // عرض النافذة المنبثقة
                    $('#serviceModal').modal('show');
                } else {
                    showAlert(response.message, 'خطأ', false);
                }
            },
            error: function(xhr, status, error) {
                showAlert('حدث خطأ أثناء تحميل بيانات الخدمة', 'خطأ', false);
                console.error(error);
            }
        });
    }
    
    // دالة لحفظ الخدمة (إضافة/تعديل)
    function saveService() {
        // التحقق من صحة النموذج
        if(!$('#service-form')[0].checkValidity()) {
            $('#service-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات الخدمة
        const serviceId = $('#service-id').val();
        const serviceData = {
            name: $('#service-name').val(),
            category: $('#service-category').val(),
            price: $('#service-price').val(),
            duration: $('#service-duration').val(),
            description: $('#service-description').val(),
            active: $('#service-active').is(':checked') ? 1 : 0
        };
        
        // تحديد نوع العملية (إضافة/تعديل)
        const url = serviceId ? 'api/services/update.php' : 'api/services/create.php';
        
        // إضافة معرف الخدمة إذا كانت عملية تعديل
        if(serviceId) {
            serviceData.id = serviceId;
        }
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-service-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // إرسال البيانات إلى الخادم
        $.ajax({
            url: url,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(serviceData),
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    // إغلاق النافذة المنبثقة
                    $('#serviceModal').modal('hide');
                    
                    // إعادة تحميل الخدمات
                    loadServices();
                    
                    // عرض رسالة نجاح
                    showAlert(serviceId ? 'تم تعديل الخدمة بنجاح' : 'تم إضافة الخدمة بنجاح', 'نجاح', false);
                } else {
                    showAlert(response.message, 'خطأ', false);
                }
                
                // إعادة تفعيل زر الحفظ
                $('#save-service-btn').prop('disabled', false).text('حفظ');
            },
            error: function(xhr, status, error) {
                showAlert('حدث خطأ أثناء حفظ الخدمة', 'خطأ', false);
                console.error(error);
                
                // إعادة تفعيل زر الحفظ
                $('#save-service-btn').prop('disabled', false).text('حفظ');
            }
        });
    }
    
    // دالة لحذف خدمة
    function deleteService(serviceId, serviceName) {
        // تأكيد الحذف
        showAlert(
            `هل أنت متأكد من حذف الخدمة "${serviceName}"؟`,
            'تأكيد الحذف',
            true,
            function() {
                $.ajax({
                    url: 'api/services/delete.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: serviceId }),
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            // إعادة تحميل الخدمات
                            loadServices();
                            
                            // عرض رسالة نجاح
                            showAlert('تم حذف الخدمة بنجاح', 'نجاح', false);
                        } else {
                            showAlert(response.message, 'خطأ', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('حدث خطأ أثناء حذف الخدمة', 'خطأ', false);
                        console.error(error);
                    }
                });
            }
        );
    }
</script>