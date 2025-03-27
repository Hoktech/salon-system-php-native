<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-file-invoice-dollar"></i> إدارة الفواتير</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary create-invoice-btn">
            <i class="fas fa-plus"></i> إنشاء فاتورة جديدة
        </button>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold">قائمة الفواتير</h6>
        <div class="d-flex">
            <div class="dropdown me-2">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter"></i> فلترة
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="filterDropdown" style="min-width: 300px;">
                    <form id="filter-form">
                        <div class="mb-3">
                            <label for="start-date" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="start-date">
                        </div>
                        <div class="mb-3">
                            <label for="end-date" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="end-date">
                        </div>
                        <div class="mb-3">
                            <label for="payment-method" class="form-label">طريقة الدفع</label>
                            <select class="form-select" id="payment-method">
                                <option value="">الكل</option>
                                <option value="cash">نقداً</option>
                                <option value="card">بطاقة ائتمان</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>
            <button class="btn btn-outline-secondary btn-sm" id="export-btn">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="invoices-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>التاريخ</th>
                        <th>العميل</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
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

<!-- Modal إنشاء فاتورة جديدة -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createInvoiceModalLabel">إنشاء فاتورة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="invoice-form">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer-id" class="form-label">العميل</label>
                                <div class="input-group">
                                    <select class="form-select" id="customer-id" name="customer_id">
                                        <option value="">اختر العميل</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" id="add-customer-btn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment-method-input" class="form-label">طريقة الدفع</label>
                                <select class="form-select" id="payment-method-input" name="payment_method">
                                    <option value="cash">نقداً</option>
                                    <option value="card">بطاقة ائتمان</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <ul class="nav nav-tabs mb-3" id="invoiceTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="true">الخدمات</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="false">المنتجات</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="invoiceTabContent">
                        <div class="tab-pane fade show active" id="services" role="tabpanel" aria-labelledby="services-tab">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <h5>الخدمات</h5>
                                    <button type="button" class="btn btn-sm btn-success" id="add-service-btn">
                                        <i class="fas fa-plus"></i> إضافة خدمة
                                    </button>
                                </div>
                                <hr>
                                <div id="services-container">
                                    <!-- سيتم إضافة الخدمات هنا ديناميكياً -->
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <h5>المنتجات</h5>
                                    <button type="button" class="btn btn-sm btn-success" id="add-product-btn">
                                        <i class="fas fa-plus"></i> إضافة منتج
                                    </button>
                                </div>
                                <hr>
                                <div id="products-container">
                                    <!-- سيتم إضافة المنتجات هنا ديناميكياً -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">ملخص الفاتورة</h5>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>إجمالي الخدمات:</span>
                                        <span id="services-total">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>إجمالي المنتجات:</span>
                                        <span id="products-total">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>المجموع:</span>
                                        <span id="subtotal">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <span class="input-group-text">خصم</span>
                                            <input type="number" class="form-control" id="discount" name="discount_amount" value="0" min="0">
                                        </div>
                                        <span id="discount-amount">0.00</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>الإجمالي النهائي:</span>
                                        <span id="final-total">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" id="save-invoice-btn">حفظ وطباعة</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal تفاصيل الفاتورة -->
<div class="modal fade" id="invoiceDetailsModal" tabindex="-1" aria-labelledby="invoiceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceDetailsModalLabel">تفاصيل الفاتورة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="invoice-details-content">
                <!-- سيتم تحميل التفاصيل هنا -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="print-invoice-btn">طباعة</button>
                <button type="button" class="btn btn-success" id="whatsapp-invoice-btn">إرسال عبر واتساب</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // تحميل الفواتير
        loadInvoices();
        
        // تحميل العملاء
        loadCustomers();
        
        // تحميل الخدمات
        loadServices();
        
        // تحميل المنتجات
        loadProducts();
        
        // فلترة الفواتير
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            loadInvoices({
                start_date: $('#start-date').val(),
                end_date: $('#end-date').val(),
                payment_method: $('#payment-method').val()
            });
        });
        
        // زر إنشاء فاتورة جديدة
        $('.create-invoice-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#invoice-form')[0].reset();
            $('#services-container').empty();
            $('#products-container').empty();
            updateTotals();
            
            // عرض النافذة المنبثقة
            $('#createInvoiceModal').modal('show');
        });
        
        // إضافة خدمة للفاتورة
        $('#add-service-btn').on('click', function() {
            addServiceRow();
        });
        
        // إضافة منتج للفاتورة
        $('#add-product-btn').on('click', function() {
            addProductRow();
        });
        
        // حفظ الفاتورة
        $('#save-invoice-btn').on('click', function() {
            saveInvoice();
        });
        
        // طباعة الفاتورة
        $('#print-invoice-btn').on('click', function() {
            const invoiceId = $(this).data('invoice-id');
            printInvoice(invoiceId);
        });
        
        // إرسال الفاتورة عبر واتساب
        $('#whatsapp-invoice-btn').on('click', function() {
            const invoiceId = $(this).data('invoice-id');
            const customerPhone = $(this).data('customer-phone');
            sendInvoiceWhatsapp(invoiceId, customerPhone);
        });
    });
    
    // دالة لتحميل الفواتير
    function loadInvoices(filters = {}) {
        // عرض مؤشر التحميل
        $('#invoices-table tbody').html('<tr><td colspan="7" class="text-center">جاري التحميل...</td></tr>');
        
        // طلب الفواتير من API
        getInvoices(filters)
            .then(response => {
                if(response.status) {
                    const invoices = response.data;
                    
                    if(invoices.length > 0) {
                        let html = '';
                        
                        invoices.forEach(invoice => {
                            // تنسيق البيانات
                            const date = new Date(invoice.invoice_date).toLocaleDateString('ar-SA');
                            const customerName = invoice.customer_name || 'عميل عام';
                            const amount = parseFloat(invoice.final_amount).toFixed(2);
                            
                            // ترجمة طريقة الدفع
                            let paymentMethod = '';
                            switch(invoice.payment_method) {
                                case 'cash':
                                    paymentMethod = 'نقداً';
                                    break;
                                case 'card':
                                    paymentMethod = 'بطاقة ائتمان';
                                    break;
                                case 'bank_transfer':
                                    paymentMethod = 'تحويل بنكي';
                                    break;
                                default:
                                    paymentMethod = 'أخرى';
                            }
                            
                            // ترجمة حالة الدفع
                            let statusClass = '';
                            let statusText = '';
                            switch(invoice.payment_status) {
                                case 'paid':
                                    statusClass = 'success';
                                    statusText = 'مدفوع';
                                    break;
                                case 'pending':
                                    statusClass = 'warning';
                                    statusText = 'معلق';
                                    break;
                                case 'cancelled':
                                    statusClass = 'danger';
                                    statusText = 'ملغي';
                                    break;
                            }
                            
                            html += `
                                <tr>
                                    <td>${invoice.invoice_number}</td>
                                    <td>${date}</td>
                                    <td>${customerName}</td>
                                    <td>${amount}</td>
                                    <td>${paymentMethod}</td>
                                    <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-invoice" data-id="${invoice.id}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary print-invoice" data-id="${invoice.id}">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#invoices-table tbody').html(html);
                    } else {
                        $('#invoices-table tbody').html('<tr><td colspan="7" class="text-center">لا توجد فواتير</td></tr>');
                    }
                } else {
                    $('#invoices-table tbody').html('<tr><td colspan="7" class="text-center text-danger">حدث خطأ أثناء تحميل الفواتير</td></tr>');
                }
            })
            .catch(error => {
                $('#invoices-table tbody').html('<tr><td colspan="7" class="text-center text-danger">حدث خطأ أثناء تحميل الفواتير</td></tr>');
                console.error(error);
            });
    }
    
    // دالة لتحميل العملاء
    function loadCustomers() {
        getCustomers()
            .then(response => {
                if(response.status) {
                    const customers = response.data;
                    
                    if(customers.length > 0) {
                        let html = '<option value="">اختر العميل</option>';
                        
                        customers.forEach(customer => {
                            html += `<option value="${customer.id}">${customer.full_name} - ${customer.phone}</option>`;
                        });
                        
                        $('#customer-id').html(html);
                    }
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    
    // دالة لتحميل الخدمات
    function loadServices() {
        getServices()
            .then(response => {
                if(response.status) {
                    // تخزين الخدمات في متغير عام
                    window.servicesData = response.data;
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    
    // دالة لتحميل المنتجات
    function loadProducts() {
        getProducts()
            .then(response => {
                if(response.status) {
                    // تخزين المنتجات في متغير عام
                    window.productsData = response.data;
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    
    // دالة لإضافة صف خدمة
    function addServiceRow() {
        if(!window.servicesData || window.servicesData.length === 0) {
            alert('لا توجد خدمات متاحة');
            return;
        }
        
        const rowId = Date.now();
        
        let servicesOptions = '<option value="">اختر خدمة</option>';
        window.servicesData.forEach(service => {
            servicesOptions += `<option value="${service.id}" data-price="${service.price}">${service.name}</option>`;
        });
        
        let employeesOptions = '<option value="">اختر موظف</option>';
        // هنا يمكن تحميل الموظفين من API
        
        const html = `
            <div class="card mb-2 service-row" id="service-row-${rowId}">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label">الخدمة</label>
                                <select class="form-select service-select" data-row-id="${rowId}">
                                    ${servicesOptions}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2">
                                <label class="form-label">السعر</label>
                                <input type="number" class="form-control service-price" data-row-id="${rowId}" value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2">
                                <label class="form-label">الكمية</label>
                                <input type="number" class="form-control service-quantity" data-row-id="${rowId}" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2">
                                <label class="form-label">الخصم</label>
                                <input type="number" class="form-control service-discount" data-row-id="${rowId}" value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2">
                                <label class="form-label">الإجمالي</label>
                                <div class="input-group">
                                    <input type="text" class="form-control service-total" data-row-id="${rowId}" value="0" readonly>
                                    <button class="btn btn-danger remove-service" type="button" data-row-id="${rowId}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label">الموظف</label>
                                <select class="form-select service-employee" data-row-id="${rowId}">
                                    ${employeesOptions}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#services-container').append(html);
        
        // إضافة مستمعات الأحداث
        $(`#service-row-${rowId} .service-select`).on('change', function() {
            const rowId = $(this).data('row-id');
            const serviceId = $(this).val();
            
            if(serviceId) {
                const service = window.servicesData.find(s => s.id == serviceId);
                if(service) {
                    $(`#service-row-${rowId} .service-price`).val(service.price);
                    updateServiceRowTotal(rowId);
                }
            } else {
                $(`#service-row-${rowId} .service-price`).val(0);
                updateServiceRowTotal(rowId);
            }
        });
        
        $(`#service-row-${rowId} .service-price, #service-row-${rowId} .service-quantity, #service-row-${rowId} .service-discount`).on('input', function() {
            const rowId = $(this).data('row-id');
            updateServiceRowTotal(rowId);
        });
        
        $(`#service-row-${rowId} .remove-service`).on('click', function() {
            const rowId = $(this).data('row-id');
            $(`#service-row-${rowId}`).remove();
            updateTotals();
        });
    }
    
    // دالة لحساب إجمالي صف الخدمة
    function updateServiceRowTotal(rowId) {
        const price = parseFloat($(`#service-row-${rowId} .service-price`).val()) || 0;
        const quantity = parseInt($(`#service-row-${rowId} .service-quantity`).val()) || 1;
        const discount = parseFloat($(`#service-row-${rowId} .service-discount`).val()) || 0;
        
        const total = (price * quantity) - discount;
        $(`#service-row-${rowId} .service-total`).val(total.toFixed(2));
        
        updateTotals();
    }
    
    // دالة لإضافة صف منتج
    function addProductRow() {
        if(!window.productsData || window.productsData.length === 0) {
            alert('لا توجد منتجات متاحة');
            return;
        }
        
        const rowId = Date.now();
        
        let productsOptions = '<option value="">اختر منتج</option>';
        window.productsData.forEach(product => {
            productsOptions += `<option value="${product.id}" data-price="${product.selling_price}" data-stock="${product.stock_quantity}">${product.name}</option>`;
        });
        
        const html = `
            <div class="card mb-2 product-row" id="product-row-${rowId}">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label">المنتج</label>
                                <select class="form-select product-select" data-row-id="${rowId}">
                                    ${productsOptions}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2">
                                <label class="form-label">السعر</label>
                                <input type="number" class="form-control product-price" data-row-id="${rowId}" value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2">
                                <label class="form-label">الكمية</label>
                                <input type="number" class="form-control product-quantity" data-row-id="${rowId}" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2">
                                <label class="form-label">الخصم</label>
                                <input type="number" class="form-control product-discount" data-row-id="${rowId}" value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2">
                                <label class="form-label">الإجمالي</label>
                                <div class="input-group">
                                    <input type="text" class="form-control product-total" data-row-id="${rowId}" value="0" readonly>
                                    <button class="btn btn-danger remove-product" type="button" data-row-id="${rowId}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted product-stock" data-row-id="${rowId}">المخزون: 0</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#products-container').append(html);
        
        // إضافة مستمعات الأحداث
        $(`#product-row-${rowId} .product-select`).on('change', function() {
            const rowId = $(this).data('row-id');
            const productId = $(this).val();
            
            if(productId) {
                const product = window.productsData.find(p => p.id == productId);
                if(product) {
                    $(`#product-row-${rowId} .product-price`).val(product.selling_price);
                    $(`#product-row-${rowId} .product-stock`).text(`المخزون: ${product.stock_quantity}`);
                    updateProductRowTotal(rowId);
                }
            } else {
                $(`#product-row-${rowId} .product-price`).val(0);
                $(`#product-row-${rowId} .product-stock`).text('المخزون: 0');
                updateProductRowTotal(rowId);
            }
        });
        
        $(`#product-row-${rowId} .product-price, #product-row-${rowId} .product-quantity, #product-row-${rowId} .product-discount`).on('input', function() {
            const rowId = $(this).data('row-id');
            updateProductRowTotal(rowId);
        });
        
        $(`#product-row-${rowId} .remove-product`).on('click', function() {
            const rowId = $(this).data('row-id');
            $(`#product-row-${rowId}`).remove();
            updateTotals();
        });
    }
    
    // دالة لحساب إجمالي صف المنتج
    function updateProductRowTotal(rowId) {
        const price = parseFloat($(`#product-row-${rowId} .product-price`).val()) || 0;
        const quantity = parseInt($(`#product-row-${rowId} .product-quantity`).val()) || 1;
        const discount = parseFloat($(`#product-row-${rowId} .product-discount`).val()) || 0;
        
        const total = (price * quantity) - discount;
        $(`#product-row-${rowId} .product-total`).val(total.toFixed(2));
        
        updateTotals();
    }
    
    // دالة لحساب إجماليات الفاتورة
    function updateTotals() {
        let servicesTotal = 0;
        let productsTotal = 0;
        
        // حساب إجمالي الخدمات
        $('.service-total').each(function() {
            servicesTotal += parseFloat($(this).val()) || 0;
        });
        
        // حساب إجمالي المنتجات
        $('.product-total').each(function() {
            productsTotal += parseFloat($(this).val()) || 0;
        });
        
        // حساب المجموع
        const subtotal = servicesTotal + productsTotal;
        
        // حساب الخصم
        const discount = parseFloat($('#discount').val()) || 0;
        
        // حساب الإجمالي النهائي
        const finalTotal = subtotal - discount;
        
        // تحديث العناصر
        $('#services-total').text(servicesTotal.toFixed(2));
        $('#products-total').text(productsTotal.toFixed(2));
        $('#subtotal').text(subtotal.toFixed(2));
        $('#discount-amount').text(discount.toFixed(2));
        $('#final-total').text(finalTotal.toFixed(2));
    }
    
    // دالة لحفظ الفاتورة
    function saveInvoice() {
        // التحقق من وجود عناصر في الفاتورة
        if($('.service-row').length === 0 && $('.product-row').length === 0) {
            alert('يجب إضافة خدمة أو منتج واحد على الأقل');
            return;
        }
        
        // جمع بيانات الفاتورة
        const customerId = $('#customer-id').val();
        const paymentMethod = $('#payment-method-input').val();
        const notes = $('#notes').val();
        const totalAmount = parseFloat($('#subtotal').text());
        const discountAmount = parseFloat($('#discount').val()) || 0;
        const finalAmount = parseFloat($('#final-total').text());
        
        // جمع بيانات الخدمات
        const services = [];
        $('.service-row').each(function() {
            const rowId = $(this).attr('id').replace('service-row-', '');
            const serviceId = $(`#service-row-${rowId} .service-select`).val();
            const employeeId = $(`#service-row-${rowId} .service-employee`).val();
            
            if(serviceId) {
                services.push({
                    service_id: serviceId,
                    employee_id: employeeId || null,
                    price: parseFloat($(`#service-row-${rowId} .service-price`).val()),
                    quantity: parseInt($(`#service-row-${rowId} .service-quantity`).val()),
                    discount: parseFloat($(`#service-row-${rowId} .service-discount`).val()) || 0,
                    total: parseFloat($(`#service-row-${rowId} .service-total`).val())
                });
            }
        });
        
        // جمع بيانات المنتجات
        const products = [];
        $('.product-row').each(function() {
            const rowId = $(this).attr('id').replace('product-row-', '');
            const productId = $(`#product-row-${rowId} .product-select`).val();
            
            if(productId) {
                products.push({
                    product_id: productId,
                    price: parseFloat($(`#product-row-${rowId} .product-price`).val()),
                    quantity: parseInt($(`#product-row-${rowId} .product-quantity`).val()),
                    discount: parseFloat($(`#product-row-${rowId} .product-discount`).val()) || 0,
                    total: parseFloat($(`#product-row-${rowId} .product-total`).val())
                });
            }
        });
        
        // التحقق من وجود عناصر صالحة
        if(services.length === 0 && products.length === 0) {
            alert('يجب اختيار خدمة أو منتج واحد على الأقل');
            return;
        }
        
        // إنشاء كائن البيانات
        const invoiceData = {
            customer_id: customerId || null,
            payment_method: paymentMethod,
            total_amount: totalAmount,
            discount_amount: discountAmount,
            final_amount: finalAmount,
            notes: notes,
            services: services,
            products: products
        };
        
        // حفظ الفاتورة
        $('#save-invoice-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        createInvoice(invoiceData)
            .then(response => {
                if(response.status) {
                    alert('تم إنشاء الفاتورة بنجاح');
                    
                    // إغلاق النافذة المنبثقة
                    $('#createInvoiceModal').modal('hide');
                    
                    // طباعة الفاتورة
                    printInvoice(response.invoice_id);
                    
                    // إعادة تحميل الفواتير
                    loadInvoices();
                } else {
                    alert('حدث خطأ أثناء إنشاء الفاتورة: ' + response.message);
                }
                
                $('#save-invoice-btn').prop('disabled', false).text('حفظ وطباعة');
            })
            .catch(error => {
                alert('حدث خطأ أثناء إنشاء الفاتورة');
                console.error(error);
                $('#save-invoice-btn').prop('disabled', false).text('حفظ وطباعة');
            });
    }
    
    // دالة لطباعة الفاتورة
    function printInvoice(invoiceId) {
        getInvoice(invoiceId)
            .then(response => {
                if(response.status) {
                    // طباعة الفاتورة باستخدام طابعة حرارية
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(response.print_html);
                    printWindow.document.close();
                    
                    // طباعة الصفحة بعد تحميلها
                    printWindow.onload = function() {
                        printWindow.print();
                    };
                }
            })
            .catch(error => {
                alert('حدث خطأ أثناء طباعة الفاتورة');
                console.error(error);
            });
    }
    
    // دالة لإرسال الفاتورة عبر واتساب
    function sendInvoiceWhatsapp(invoiceId, phone) {
        if(!phone) {
            alert('لا يوجد رقم هاتف للعميل');
            return;
        }
        
        // إزالة أي أحرف غير رقمية
        phone = phone.replace(/\D/g, '');
        
        // التأكد من إضافة رمز الدولة إذا لم يكن موجوداً
        if(!phone.startsWith('966')) {
            phone = '966' + phone.replace(/^0+/, '');
        }
        
        // إنشاء رابط الواتساب
        const whatsappUrl = `https://wa.me/${phone}?text=شكراً لزيارتكم! يمكنكم الاطلاع على فاتورتكم من خلال الرابط: ${window.location.origin}/view_invoice.php?id=${invoiceId}`;
        
        // فتح رابط الواتساب في نافذة جديدة
        window.open(whatsappUrl, '_blank');
    }
</script>