<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-users"></i> إدارة العملاء</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" id="add-customer-btn">
            <i class="fas fa-plus"></i> إضافة عميل جديد
        </button>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold">قائمة العملاء</h6>
        <div class="d-flex">
            <div class="input-group input-group-sm me-2" style="width: 200px;">
                <input type="text" class="form-control" id="search-customer" placeholder="بحث...">
                <button class="btn btn-outline-secondary" type="button" id="clear-search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <button class="btn btn-outline-secondary btn-sm" id="export-customers">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="customers-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ الميلاد</th>
                        <th>النوع</th>
                        <th>نقاط الولاء</th>
                        <th>تاريخ التسجيل</th>
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

<!-- Modal إضافة/تعديل عميل -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">إضافة عميل جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customer-form">
                    <input type="hidden" id="customer-id">
                    <div class="mb-3">
                        <label for="full-name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full-name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">تاريخ الميلاد</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate">
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">النوع <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="male">ذكر</option>
                            <option value="female">أنثى</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-customer-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal تفاصيل العميل -->
<div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerDetailsModalLabel">تفاصيل العميل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">معلومات العميل</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>الاسم:</strong> <span id="customer-details-name"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>الهاتف:</strong> <span id="customer-details-phone"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>البريد الإلكتروني:</strong> <span id="customer-details-email"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>تاريخ الميلاد:</strong> <span id="customer-details-birthdate"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>النوع:</strong> <span id="customer-details-gender"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>العنوان:</strong> <span id="customer-details-address"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>نقاط الولاء:</strong> <span id="customer-details-loyalty"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>تاريخ التسجيل:</strong> <span id="customer-details-created"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>ملاحظات:</strong> <p id="customer-details-notes"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">معلومات الزيارات</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>عدد الزيارات:</strong> <span id="customer-details-visits"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>آخر زيارة:</strong> <span id="customer-details-last-visit"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>إجمالي المدفوعات:</strong> <span id="customer-details-total-spent"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>متوسط الإنفاق:</strong> <span id="customer-details-avg-spent"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">الخدمات المفضلة</h6>
                            </div>
                            <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                <ul class="list-group list-group-flush" id="customer-details-services">
                                    <!-- سيتم تحميل البيانات هنا عبر AJAX -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">آخر الزيارات</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="customer-visits-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الخدمات</th>
                                        <th>المبلغ</th>
                                        <th>الموظف</th>
                                        <th>ملاحظات</th>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-success" id="add-appointment-btn">
                    <i class="fas fa-calendar-plus"></i> إضافة موعد
                </button>
                <button type="button" class="btn btn-primary" id="add-invoice-btn">
                    <i class="fas fa-file-invoice-dollar"></i> إنشاء فاتورة
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // تحميل العملاء
        loadCustomers();
        
        // زر إضافة عميل جديد
        $('#add-customer-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#customer-form')[0].reset();
            $('#customer-id').val('');
            
            // تغيير عنوان النافذة المنبثقة
            $('#customerModalLabel').text('إضافة عميل جديد');
            
            // عرض النافذة المنبثقة
            $('#customerModal').modal('show');
        });
        
        // زر حفظ العميل
        $('#save-customer-btn').on('click', function() {
            saveCustomer();
        });
        
        // البحث عن عميل
        $('#search-customer').on('input', function() {
            const searchText = $(this).val().toLowerCase();
            
            $('#customers-table tbody tr').each(function() {
                const name = $(this).find('td:eq(0)').text().toLowerCase();
                const phone = $(this).find('td:eq(1)').text().toLowerCase();
                const email = $(this).find('td:eq(2)').text().toLowerCase();
                
                if(name.includes(searchText) || phone.includes(searchText) || email.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // زر مسح البحث
        $('#clear-search').on('click', function() {
            $('#search-customer').val('');
            $('#customers-table tbody tr').show();
        });
        
        // زر تصدير العملاء
        $('#export-customers').on('click', function() {
            exportCustomers();
        });
    });
    
    // دالة لتحميل العملاء
    function loadCustomers() {
        // عرض مؤشر التحميل
        $('#customers-table tbody').html('<tr><td colspan="8" class="text-center">جاري التحميل...</td></tr>');
        
        // طلب العملاء من API
        getCustomers()
            .then(response => {
                if(response.status) {
                    const customers = response.data;
                    
                    if(customers.length > 0) {
                        let html = '';
                        
                        customers.forEach(customer => {
                            // تنسيق البيانات
                            const birthdate = customer.birthdate ? new Date(customer.birthdate).toLocaleDateString('ar-SA') : '-';
                            const gender = customer.gender === 'male' ? 'ذكر' : 'أنثى';
                            const createdAt = new Date(customer.created_at).toLocaleDateString('ar-SA');
                            
                            html += `
                                <tr>
                                    <td>${customer.full_name}</td>
                                    <td>${customer.phone}</td>
                                    <td>${customer.email || '-'}</td>
                                    <td>${birthdate}</td>
                                    <td>${gender}</td>
                                    <td>${customer.loyalty_points}</td>
                                    <td>${createdAt}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-customer" data-id="${customer.id}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary edit-customer" data-id="${customer.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-customer" data-id="${customer.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#customers-table tbody').html(html);
                        
                        // إضافة مستمعات الأحداث
                        $('.view-customer').on('click', function() {
                            const customerId = $(this).data('id');
                            viewCustomer(customerId);
                        });
                        
                        $('.edit-customer').on('click', function() {
                            const customerId = $(this).data('id');
                            editCustomer(customerId);
                        });
                        
                        $('.delete-customer').on('click', function() {
                            const customerId = $(this).data('id');
                            deleteCustomer(customerId);
                        });
                    } else {
                        $('#customers-table tbody').html('<tr><td colspan="8" class="text-center">لا يوجد عملاء</td></tr>');
                    }
                } else {
                    $('#customers-table tbody').html('<tr><td colspan="8" class="text-center text-danger">حدث خطأ أثناء تحميل العملاء</td></tr>');
                }
            })
            .catch(error => {
                $('#customers-table tbody').html('<tr><td colspan="8" class="text-center text-danger">حدث خطأ أثناء تحميل العملاء</td></tr>');
                console.error(error);
            });
    }
    
    // دالة لحفظ العميل
    function saveCustomer() {
        // التحقق من صحة النموذج
        if(!$('#customer-form')[0].checkValidity()) {
            $('#customer-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات العميل
        const customerId = $('#customer-id').val();
        const customerData = {
            full_name: $('#full-name').val(),
            phone: $('#phone').val(),
            email: $('#email').val(),
            birthdate: $('#birthdate').val(),
            gender: $('#gender').val(),
            address: $('#address').val(),
            notes: $('#notes').val()
        };
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-customer-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // حفظ العميل
        if(customerId) {
            // تعديل عميل موجود
            updateCustomer(customerId, customerData)
                .then(response => {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#customerModal').modal('hide');
                        
                        // إعادة تحميل العملاء
                        loadCustomers();
                        
                        // عرض رسالة نجاح
                        alert('تم تعديل العميل بنجاح');
                    } else {
                        alert('حدث خطأ أثناء تعديل العميل: ' + response.message);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-customer-btn').prop('disabled', false).text('حفظ');
                })
                .catch(error => {
                    alert('حدث خطأ أثناء تعديل العميل');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-customer-btn').prop('disabled', false).text('حفظ');
                });
        } else {
            // إضافة عميل جديد
            addCustomer(customerData)
                .then(response => {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#customerModal').modal('hide');
                        
                        // إعادة تحميل العملاء
                        loadCustomers();
                        
                        // عرض رسالة نجاح
                        alert('تم إضافة العميل بنجاح');
                    } else {
                        alert('حدث خطأ أثناء إضافة العميل: ' + response.message);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-customer-btn').prop('disabled', false).text('حفظ');
                })
                .catch(error => {
                    alert('حدث خطأ أثناء إضافة العميل');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-customer-btn').prop('disabled', false).text('حفظ');
                });
        }
    }
    
// Mejorar función para ver detalles del cliente para manejar errores
function viewCustomer(customerId) {
    // عرض مؤشر التحميل والنافذة المنبثقة
    $('#customerDetailsModal').modal('show');
    const modalBody = $('#customerDetailsModal .modal-body');
    modalBody.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
    
    // طلب بيانات العميل من API
    fetch(`api/customers/read_one.php?id=${customerId}`)
        .then(response => response.json())
        .then(response => {
            console.log("API Response:", response); // للتشخيص
            
            if(response.status) {
                const customer = response.data.customer;
                const visits = response.data.visits || [];
                const favoriteServices = response.data.favorite_services || [];
                const totalSpent = response.data.total_spent || 0;
                const avgSpent = response.data.avg_spent || 0;
                
                // إعادة بناء محتوى النافذة المنبثقة
                modalBody.html(`
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">معلومات العميل</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>الاسم:</strong> <span id="customer-details-name">${customer.full_name}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>الهاتف:</strong> <span id="customer-details-phone">${customer.phone}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>البريد الإلكتروني:</strong> <span id="customer-details-email">${customer.email || '-'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>تاريخ الميلاد:</strong> <span id="customer-details-birthdate">${customer.birthdate ? new Date(customer.birthdate).toLocaleDateString('ar-SA') : '-'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>النوع:</strong> <span id="customer-details-gender">${customer.gender === 'male' ? 'ذكر' : 'أنثى'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>العنوان:</strong> <span id="customer-details-address">${customer.address || '-'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>نقاط الولاء:</strong> <span id="customer-details-loyalty">${customer.loyalty_points}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>تاريخ التسجيل:</strong> <span id="customer-details-created">${new Date(customer.created_at).toLocaleDateString('ar-SA')}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>ملاحظات:</strong> <p id="customer-details-notes">${customer.notes || '-'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">معلومات الزيارات</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>عدد الزيارات:</strong> <span id="customer-details-visits">${visits.length}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>آخر زيارة:</strong> <span id="customer-details-last-visit">${visits.length > 0 ? new Date(visits[0].date).toLocaleDateString('ar-SA') : '-'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>إجمالي المدفوعات:</strong> <span id="customer-details-total-spent">${totalSpent.toFixed(2)}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>متوسط الإنفاق:</strong> <span id="customer-details-avg-spent">${avgSpent.toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">الخدمات المفضلة</h6>
                                </div>
                                <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush" id="customer-details-services">
                                        ${favoriteServices.length > 0 ? 
                                            favoriteServices.map(service => `
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    ${service.name}
                                                    <span class="badge bg-primary rounded-pill">${service.count}</span>
                                                </li>
                                            `).join('') : 
                                            '<li class="list-group-item">لا توجد خدمات</li>'
                                        }
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">آخر الزيارات</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="customer-visits-table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>التاريخ</th>
                                            <th>الخدمات</th>
                                            <th>المبلغ</th>
                                            <th>الموظف</th>
                                            <th>ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${visits.length > 0 ? 
                                            visits.map(visit => `
                                                <tr>
                                                    <td>${new Date(visit.date).toLocaleDateString('ar-SA')}</td>
                                                    <td>${visit.services || '-'}</td>
                                                    <td>${visit.amount ? visit.amount.toFixed(2) : '0.00'}</td>
                                                    <td>${visit.employee || '-'}</td>
                                                    <td>${visit.notes || '-'}</td>
                                                </tr>
                                            `).join('') : 
                                            '<tr><td colspan="5" class="text-center">لا توجد زيارات</td></tr>'
                                        }
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `);
                
                // تخزين معرف العميل في أزرار الإجراءات
                $('#add-appointment-btn').data('customer-id', customerId);
                $('#add-invoice-btn').data('customer-id', customerId);
                
            } else {
                alert('حدث خطأ أثناء تحميل بيانات العميل: ' + response.message);
                $('#customerDetailsModal').modal('hide');
            }
        })
        .catch(error => {
            console.error('Error fetching customer details:', error);
            modalBody.html(`
                <div class="alert alert-danger">
                    حدث خطأ أثناء تحميل بيانات العميل. الرجاء المحاولة مرة أخرى.
                </div>
            `);
        });
}
// Mejorar función de edición de cliente
function editCustomer(customerId) {
    // Mostrar indicador de carga
    $('#customerModal .modal-body').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">جاري التحميل...</span></div></div>');
    $('#customerModal').modal('show');

    // Solicitar datos del cliente a la API
    getCustomer(customerId)
        .then(response => {
            if(response.status) {
                const customer = response.data;
                
                // Reconstruir el contenido del modal
                $('#customerModal .modal-body').html(`
                    <form id="customer-form">
                        <input type="hidden" id="customer-id">
                        <div class="mb-3">
                            <label for="full-name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full-name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="birthdate" class="form-label">تاريخ الميلاد</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate">
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">النوع <span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                `);
                
                // Llenar el formulario con datos del cliente
                $('#customer-id').val(customer.id);
                $('#full-name').val(customer.full_name);
                $('#phone').val(customer.phone);
                $('#email').val(customer.email);
                $('#birthdate').val(customer.birthdate);
                $('#gender').val(customer.gender);
                $('#address').val(customer.address);
                $('#notes').val(customer.notes);
                
                // Cambiar título del modal
                $('#customerModalLabel').text('تعديل العميل');
                
                // El modal ya está abierto
            } else {
                $('#customerModal').modal('hide');
                alert('حدث خطأ أثناء تحميل بيانات العميل: ' + response.message);
            }
        })
        .catch(error => {
            $('#customerModal').modal('hide');
            alert('حدث خطأ أثناء تحميل بيانات العميل');
            console.error(error);
        });
    }

    // دالة لحذف العميل
    function deleteCustomer(customerId) {
        // التأكيد قبل الحذف
        if(!confirm('هل أنت متأكد من حذف هذا العميل؟')) {
            return;
        }
        
        // طلب حذف العميل من API
        deleteCustomer(customerId)
            .then(response => {
                if(response.status) {
                    // إعادة تحميل العملاء
                    loadCustomers();
                    
                    // عرض رسالة نجاح
                    alert('تم حذف العميل بنجاح');
                } else {
                    alert('حدث خطأ أثناء حذف العميل: ' + response.message);
                }
            })
            .catch(error => {
                alert('حدث خطأ أثناء حذف العميل');
                console.error(error);
            });
    }
    
    // دالة لتصدير العملاء
    function exportCustomers() {
        window.location.href = 'api/customers/export.php';
    }
</script>