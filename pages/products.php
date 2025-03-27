<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-shopping-cart"></i> إدارة المنتجات والمخزون</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" id="add-product-btn">
            <i class="fas fa-plus"></i> إضافة منتج جديد
        </button>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold">قائمة المنتجات</h6>
        <div class="d-flex">
            <div class="input-group input-group-sm me-2" style="width: 200px;">
                <input type="text" class="form-control" id="search-product" placeholder="بحث...">
                <button class="btn btn-outline-secondary" type="button" id="clear-search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="dropdown me-2">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter"></i> فلترة
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                    <h6 class="dropdown-header">الفئة</h6>
                    <div class="px-3 py-1">
                        <select class="form-select form-select-sm" id="category-filter">
                            <option value="">الكل</option>
                            <!-- سيتم تحميل الفئات هنا عبر AJAX -->
                        </select>
                    </div>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">حالة المخزون</h6>
                    <div class="px-3 py-1">
                        <select class="form-select form-select-sm" id="stock-filter">
                            <option value="">الكل</option>
                            <option value="low">منخفض</option>
                            <option value="out">نفد</option>
                            <option value="normal">طبيعي</option>
                        </select>
                    </div>
                </div>
            </div>
            <button class="btn btn-outline-secondary btn-sm" id="export-products">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="products-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الفئة</th>
                        <th>سعر الشراء</th>
                        <th>سعر البيع</th>
                        <th>المخزون</th>
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

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold">تنبيهات المخزون</h6>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" id="refresh-alerts-btn">
                        <i class="fas fa-sync-alt"></i> تحديث
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="alerts-table" width="100%" cellspacing="0">
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

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold">إحصائيات المخزون</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            إجمالي قيمة المخزون</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-inventory-value">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            عدد المنتجات</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-products-count">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            منتجات منخفضة المخزون</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="low-stock-count">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            منتجات نفدت</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="out-of-stock-count">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <canvas id="inventory-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة/تعديل منتج -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">إضافة منتج جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="product-form">
                    <input type="hidden" id="product-id">
                    <div class="mb-3">
                        <label for="product-name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="product-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="product-category" class="form-label">الفئة</label>
                        <div class="input-group">
                            <select class="form-select" id="product-category" name="category">
                                <option value="">اختر الفئة</option>
                                <!-- سيتم تحميل الفئات هنا عبر AJAX -->
                            </select>
                            <button class="btn btn-outline-secondary" type="button" id="add-category-btn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product-purchase-price" class="form-label">سعر الشراء</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="product-purchase-price" name="purchase_price" step="0.01" min="0">
                                    <span class="input-group-text">ريال</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product-selling-price" class="form-label">سعر البيع <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="product-selling-price" name="selling_price" step="0.01" min="0" required>
                                    <span class="input-group-text">ريال</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product-stock-quantity" class="form-label">الكمية في المخزون</label>
                                <input type="number" class="form-control" id="product-stock-quantity" name="stock_quantity" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product-minimum-quantity" class="form-label">الحد الأدنى للمخزون</label>
                                <input type="number" class="form-control" id="product-minimum-quantity" name="minimum_quantity" min="1" value="5">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="product-for-sale" name="for_sale" checked>
                        <label class="form-check-label" for="product-for-sale">معروض للبيع</label>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="product-for-internal-use" name="for_internal_use">
                        <label class="form-check-label" for="product-for-internal-use">للاستخدام الداخلي</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-product-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة فئة جديدة -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">إضافة فئة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="category-form">
                    <div class="mb-3">
                        <label for="category-name" class="form-label">اسم الفئة</label>
                        <input type="text" class="form-control" id="category-name" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-category-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal تعديل المخزون -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockModalLabel">تعديل المخزون</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stock-form">
                    <input type="hidden" id="stock-product-id">
                    <div class="mb-3">
                        <h5 id="stock-product-name" class="text-center mb-3"></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="current-stock" class="form-label">المخزون الحالي</label>
                                <input type="number" class="form-control" id="current-stock" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="minimum-stock" class="form-label">الحد الأدنى</label>
                                <input type="number" class="form-control" id="minimum-stock" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="stock-operation" class="form-label">العملية</label>
                        <select class="form-select" id="stock-operation">
                            <option value="add">إضافة للمخزون</option>
                            <option value="subtract">سحب من المخزون</option>
                            <option value="set">تعيين قيمة محددة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stock-quantity" class="form-label">الكمية</label>
                        <input type="number" class="form-control" id="stock-quantity" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock-notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="stock-notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-stock-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // تحميل المنتجات
        loadProducts();
        
        // تحميل تنبيهات المخزون
        loadInventoryAlerts();
        
        // تحميل إحصائيات المخزون
        loadInventoryStats();
        
        // تحميل الفئات
        loadCategories();
        
        // زر إضافة منتج جديد
        $('#add-product-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#product-form')[0].reset();
            $('#product-id').val('');
            
            // تغيير عنوان النافذة المنبثقة
            $('#productModalLabel').text('إضافة منتج جديد');
            
            // عرض النافذة المنبثقة
            $('#productModal').modal('show');
        });
        
        // زر إضافة فئة جديدة
        $('#add-category-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#category-form')[0].reset();
            
            // عرض النافذة المنبثقة
            $('#categoryModal').modal('show');
        });
        
        // زر حفظ المنتج
        $('#save-product-btn').on('click', function() {
            saveProduct();
        });
        
        // زر حفظ الفئة
        $('#save-category-btn').on('click', function() {
            saveCategory();
        });
        
        // زر حفظ المخزون
        $('#save-stock-btn').on('click', function() {
            saveStock();
        });
        
        // زر تحديث تنبيهات المخزون
        $('#refresh-alerts-btn').on('click', function() {
            loadInventoryAlerts();
        });
        
        // البحث عن منتج
        $('#search-product').on('input', function() {
            const searchText = $(this).val().toLowerCase();
            
            $('#products-table tbody tr').each(function() {
                const name = $(this).find('td:eq(0)').text().toLowerCase();
                const category = $(this).find('td:eq(1)').text().toLowerCase();
                
                if(name.includes(searchText) || category.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // زر مسح البحث
        $('#clear-search').on('click', function() {
            $('#search-product').val('');
            $('#products-table tbody tr').show();
        });
        
        // فلترة المنتجات حسب الفئة
        $('#category-filter').on('change', function() {
            filterProducts();
        });
        
        // فلترة المنتجات حسب حالة المخزون
        $('#stock-filter').on('change', function() {
            filterProducts();
        });
        
        // زر تصدير المنتجات
        $('#export-products').on('click', function() {
            exportProducts();
        });
    });
    
    // دالة لتحميل المنتجات
    function loadProducts() {
        // عرض مؤشر التحميل
        $('#products-table tbody').html('<tr><td colspan="8" class="text-center">جاري التحميل...</td></tr>');
        
        // طلب المنتجات من API
        $.ajax({
            url: 'api/products/read.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const products = response.data;
                    
                    // تخزين المنتجات في متغير عام
                    window.productsData = products;
                    
                    if(products.length > 0) {
                        let html = '';
                        
                        products.forEach(product => {
                            // تحديد حالة المخزون
                            let stockStatus = 'normal';
                            let stockClass = 'success';
                            let stockText = 'طبيعي';
                            
                            if(product.stock_quantity <= 0) {
                                stockStatus = 'out';
                                stockClass = 'danger';
                                stockText = 'نفد';
                            } else if(product.stock_quantity < product.minimum_quantity) {
                                stockStatus = 'low';
                                stockClass = 'warning';
                                stockText = 'منخفض';
                            }
                            
                            html += `
                                <tr data-id="${product.id}" data-category="${product.category}" data-stock-status="${stockStatus}">
                                    <td>${product.name}</td>
                                    <td>${product.category || '-'}</td>
                                    <td>${product.purchase_price ? product.purchase_price.toFixed(2) : '-'}</td>
                                    <td>${product.selling_price.toFixed(2)}</td>
                                    <td>${product.stock_quantity}</td>
                                    <td>${product.minimum_quantity}</td>
                                    <td><span class="badge bg-${stockClass}">${stockText}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-stock" data-id="${product.id}">
                                            <i class="fas fa-boxes"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary edit-product" data-id="${product.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-product" data-id="${product.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#products-table tbody').html(html);
                        
                        // إضافة مستمعات الأحداث
                        $('.edit-product').on('click', function() {
                            const productId = $(this).data('id');
                            editProduct(productId);
                        });
                        
                        $('.delete-product').on('click', function() {
                            const productId = $(this).data('id');
                            deleteProduct(productId);
                        });
                        
                        $('.edit-stock').on('click', function() {
                            const productId = $(this).data('id');
                            editStock(productId);
                        });
                    } else {
                        $('#products-table tbody').html('<tr><td colspan="8" class="text-center">لا توجد منتجات</td></tr>');
                    }
                } else {
                    $('#products-table tbody').html('<tr><td colspan="8" class="text-center text-danger">حدث خطأ أثناء تحميل المنتجات</td></tr>');
                }
            },
            error: function(error) {
                $('#products-table tbody').html('<tr><td colspan="8" class="text-center text-danger">حدث خطأ أثناء تحميل المنتجات</td></tr>');
                console.error(error);
            }
        });
    }
    
    // دالة لتحميل الفئات
    function loadCategories() {
        $.ajax({
            url: 'api/categories/read.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const categories = response.data;
                    
                    if(categories.length > 0) {
                        let productCategoryHtml = '<option value="">اختر الفئة</option>';
                        let filterCategoryHtml = '<option value="">الكل</option>';
                        
                        categories.forEach(category => {
                            productCategoryHtml += `<option value="${category.name}">${category.name}</option>`;
                            filterCategoryHtml += `<option value="${category.name}">${category.name}</option>`;
                        });
                        
                        $('#product-category').html(productCategoryHtml);
                        $('#category-filter').html(filterCategoryHtml);
                    }
                }
            },
            error: function(error) {
                console.error('Error loading categories:', error);
            }
        });
    }
    
    // دالة لتحميل تنبيهات المخزون
    function loadInventoryAlerts() {
        $('#alerts-table tbody').html('<tr><td colspan="5" class="text-center">جاري التحميل...</td></tr>');
        
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
                            let stockClass = '';
                            let stockText = '';
                            
                            if(alert.stock_quantity <= 0) {
                                stockClass = 'danger';
                                stockText = 'نفد';
                            } else if(alert.stock_quantity < alert.minimum_quantity) {
                                stockClass = 'warning';
                                stockText = 'منخفض';
                            }
                            
                            html += `
                                <tr>
                                    <td>${alert.name}</td>
                                    <td>${alert.stock_quantity}</td>
                                    <td>${alert.minimum_quantity}</td>
                                    <td><span class="badge bg-${stockClass}">${stockText}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info restock-product" data-id="${alert.id}">
                                            <i class="fas fa-plus"></i> إعادة تعبئة
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#alerts-table tbody').html(html);
                        
                        // إضافة مستمعات الأحداث
                        $('.restock-product').on('click', function() {
                            const productId = $(this).data('id');
                            restockProduct(productId);
                        });
                    } else {
                        $('#alerts-table tbody').html('<tr><td colspan="5" class="text-center">لا توجد تنبيهات</td></tr>');
                    }
                } else {
                    $('#alerts-table tbody').html('<tr><td colspan="5" class="text-center text-danger">حدث خطأ أثناء تحميل التنبيهات</td></tr>');
                }
            },
            error: function(error) {
                $('#alerts-table tbody').html('<tr><td colspan="5" class="text-center text-danger">حدث خطأ أثناء تحميل التنبيهات</td></tr>');
                console.error(error);
            }
        });
    }
    
    // دالة لتحميل إحصائيات المخزون
    function loadInventoryStats() {
        $.ajax({
            url: 'api/inventory/stats.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const stats = response.data;
                    
                    // تحديث الإحصائيات
                    $('#total-inventory-value').text(stats.total_value.toFixed(2));
                    $('#total-products-count').text(stats.total_products);
                    $('#low-stock-count').text(stats.low_stock_count);
                    $('#out-of-stock-count').text(stats.out_of_stock_count);
                    
                    // إنشاء الرسم البياني
                    createInventoryChart(stats.categories);
                }
            },
            error: function(error) {
                console.error('Error loading inventory stats:', error);
            }
        });
    }
    
    // دالة لإنشاء الرسم البياني
    function createInventoryChart(categories) {
        // التحقق من وجود الرسم البياني السابق وتدميره
        if(window.inventoryChart) {
            window.inventoryChart.destroy();
        }
        
        // التحقق من وجود بيانات
        if(!categories || categories.length === 0) {
            return;
        }
        
        // إعداد البيانات
        const labels = categories.map(item => item.category || 'بدون فئة');
        const values = categories.map(item => item.value);
        
        // الألوان
        const colors = [
            'rgba(78, 115, 223, 0.8)',
            'rgba(28, 200, 138, 0.8)',
            'rgba(54, 185, 204, 0.8)',
            'rgba(246, 194, 62, 0.8)',
            'rgba(231, 74, 59, 0.8)',
            'rgba(116, 90, 242, 0.8)',
            'rgba(32, 168, 216, 0.8)'
        ];
        
        // إنشاء الرسم البياني
        const ctx = document.getElementById('inventory-chart').getContext('2d');
        window.inventoryChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, categories.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'قيمة المخزون حسب الفئة'
                    }
                }
            }
        });
    }
    
    // دالة لحفظ المنتج
    function saveProduct() {
        // التحقق من صحة النموذج
        if(!$('#product-form')[0].checkValidity()) {
            $('#product-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات المنتج
        const productId = $('#product-id').val();
        const productData = {
            name: $('#product-name').val(),
            category: $('#product-category').val(),
            purchase_price: $('#product-purchase-price').val() || null,
            selling_price: $('#product-selling-price').val(),
            stock_quantity: $('#product-stock-quantity').val() || 0,
            minimum_quantity: $('#product-minimum-quantity').val() || 5,
            for_sale: $('#product-for-sale').is(':checked') ? 1 : 0,
            for_internal_use: $('#product-for-internal-use').is(':checked') ? 1 : 0
        };
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-product-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // حفظ المنتج
        if(productId) {
            // تعديل منتج موجود
            $.ajax({
                url: 'api/products/update.php?id=' + productId,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(productData),
                success: function(response) {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#productModal').modal('hide');
                        
                        // إعادة تحميل المنتجات
                        loadProducts();
                        
                        // إعادة تحميل تنبيهات المخزون
                        loadInventoryAlerts();
                        
                        // إعادة تحميل إحصائيات المخزون
                        loadInventoryStats();
                        
                        // عرض رسالة نجاح
                        alert('تم تعديل المنتج بنجاح');
                    } else {
                        alert('حدث خطأ أثناء تعديل المنتج: ' + response.message);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-product-btn').prop('disabled', false).text('حفظ');
                },
                error: function(error) {
                    alert('حدث خطأ أثناء تعديل المنتج');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-product-btn').prop('disabled', false).text('حفظ');
                }
            });
        } else {
            // إضافة منتج جديد
            $.ajax({
                url: 'api/products/create.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(productData),
                success: function(response) {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#productModal').modal('hide');
                        
                        // إعادة تحميل المنتجات
                        loadProducts();
                        
                        // إعادة تحميل إحصائيات المخزون
                        loadInventoryStats();
                        
                        // عرض رسالة نجاح
                        alert('تم إضافة المنتج بنجاح');
                    } else {
                        alert('حدث خطأ أثناء إضافة المنتج: ' + response.message);
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-product-btn').prop('disabled', false).text('حفظ');
                },
                error: function(error) {
                    alert('حدث خطأ أثناء إضافة المنتج');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-product-btn').prop('disabled', false).text('حفظ');
                }
            });
        }
    }
    
    // دالة لحفظ الفئة
    function saveCategory() {
        // التحقق من صحة النموذج
        if(!$('#category-form')[0].checkValidity()) {
            $('#category-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات الفئة
        const categoryName = $('#category-name').val();
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-category-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // حفظ الفئة
        $.ajax({
            url: 'api/categories/create.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ name: categoryName }),
            success: function(response) {
                if(response.status) {
                    // إغلاق النافذة المنبثقة
                    $('#categoryModal').modal('hide');
                    
                    // إعادة تحميل الفئات
                    loadCategories();
                    
                    // إضافة الفئة الجديدة للقائمة المنسدلة
                    $('#product-category').append(`<option value="${categoryName}">${categoryName}</option>`);
                    $('#product-category').val(categoryName);
                    
                    // عرض رسالة نجاح
                    alert('تم إضافة الفئة بنجاح');
                } else {
                    alert('حدث خطأ أثناء إضافة الفئة: ' + response.message);
                }
                
                // إعادة تفعيل زر الحفظ
                $('#save-category-btn').prop('disabled', false).text('حفظ');
            },
            error: function(error) {
                alert('حدث خطأ أثناء إضافة الفئة');
                console.error(error);
                
                // إعادة تفعيل زر الحفظ
                $('#save-category-btn').prop('disabled', false).text('حفظ');
            }
        });
    }
    
    // دالة لتعديل المنتج
    function editProduct(productId) {
        // البحث عن المنتج في البيانات
        const product = window.productsData.find(p => p.id == productId);
        
        if(product) {
            // تعبئة نموذج المنتج بالبيانات
            $('#product-id').val(product.id);
            $('#product-name').val(product.name);
            $('#product-category').val(product.category || '');
            $('#product-purchase-price').val(product.purchase_price || '');
            $('#product-selling-price').val(product.selling_price);
            $('#product-stock-quantity').val(product.stock_quantity);
            $('#product-minimum-quantity').val(product.minimum_quantity);
            $('#product-for-sale').prop('checked', product.for_sale == 1);
            $('#product-for-internal-use').prop('checked', product.for_internal_use == 1);
            
            // تغيير عنوان النافذة المنبثقة
            $('#productModalLabel').text('تعديل المنتج');
            
            // عرض النافذة المنبثقة
            $('#productModal').modal('show');
        } else {
            alert('المنتج غير موجود');
        }
    }
    
    // دالة لحذف المنتج
    function deleteProduct(productId) {
        if(confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
            $.ajax({
                url: 'api/products/delete.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: productId }),
                success: function(response) {
                    if(response.status) {
                        // إعادة تحميل المنتجات
                        loadProducts();
                        
                        // إعادة تحميل تنبيهات المخزون
                        loadInventoryAlerts();
                        
                        // إعادة تحميل إحصائيات المخزون
                        loadInventoryStats();
                        
                        // عرض رسالة نجاح
                        alert('تم حذف المنتج بنجاح');
                    } else {
                        alert('حدث خطأ أثناء حذف المنتج: ' + response.message);
                    }
                },
                error: function(error) {
                    alert('حدث خطأ أثناء حذف المنتج');
                    console.error(error);
                }
            });
        }
    }
    
    // دالة لتعديل المخزون
    function editStock(productId) {
        // البحث عن المنتج في البيانات
        const product = window.productsData.find(p => p.id == productId);
        
        if(product) {
            // تعبئة نموذج المخزون بالبيانات
            $('#stock-product-id').val(product.id);
            $('#stock-product-name').text(product.name);
            $('#current-stock').val(product.stock_quantity);
            $('#minimum-stock').val(product.minimum_quantity);
            $('#stock-quantity').val(1);
            $('#stock-operation').val('add');
            $('#stock-notes').val('');
            
            // عرض النافذة المنبثقة
            $('#stockModal').modal('show');
        } else {
            alert('المنتج غير موجود');
        }
    }
    
    // دالة لإعادة تعبئة المنتج
    function restockProduct(productId) {
        // البحث عن المنتج في البيانات
        const product = window.productsData.find(p => p.id == productId);
        
        if(product) {
            // تعبئة نموذج المخزون بالبيانات
            $('#stock-product-id').val(product.id);
            $('#stock-product-name').text(product.name);
            $('#current-stock').val(product.stock_quantity);
            $('#minimum-stock').val(product.minimum_quantity);
            $('#stock-quantity').val(product.minimum_quantity * 2 - product.stock_quantity); // اقتراح كمية للتعبئة
            $('#stock-operation').val('add');
            $('#stock-notes').val('إعادة تعبئة المخزون');
            
            // عرض النافذة المنبثقة
            $('#stockModal').modal('show');
        } else {
            alert('المنتج غير موجود');
        }
    }
    
    // دالة لحفظ تعديلات المخزون
    function saveStock() {
        // التحقق من صحة النموذج
        if($('#stock-quantity').val() <= 0) {
            alert('يجب أن تكون الكمية أكبر من الصفر');
            return;
        }
        
        // جمع بيانات المخزون
        const productId = $('#stock-product-id').val();
        const operation = $('#stock-operation').val();
        const quantity = parseInt($('#stock-quantity').val());
        const minimumQuantity = parseInt($('#minimum-stock').val());
        const notes = $('#stock-notes').val();
        
        // حساب الكمية الجديدة
        let newQuantity;
        const currentQuantity = parseInt($('#current-stock').val());
        
        switch(operation) {
            case 'add':
                newQuantity = currentQuantity + quantity;
                break;
            case 'subtract':
                newQuantity = Math.max(0, currentQuantity - quantity);
                break;
            case 'set':
                newQuantity = quantity;
                break;
        }
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-stock-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // حفظ التعديلات
        $.ajax({
            url: 'api/inventory/update.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                product_id: productId,
                operation: operation,
                quantity: quantity,
                minimum_quantity: minimumQuantity,
                notes: notes
            }),
            success: function(response) {
                if(response.status) {
                    // إغلاق النافذة المنبثقة
                    $('#stockModal').modal('hide');
                    
                    // إعادة تحميل المنتجات
                    loadProducts();
                    
                    // إعادة تحميل تنبيهات المخزون
                    loadInventoryAlerts();
                    
                    // إعادة تحميل إحصائيات المخزون
                    loadInventoryStats();
                    
                    // عرض رسالة نجاح
                    alert('تم تعديل المخزون بنجاح');
                } else {
                    alert('حدث خطأ أثناء تعديل المخزون: ' + response.message);
                }
                
                // إعادة تفعيل زر الحفظ
                $('#save-stock-btn').prop('disabled', false).text('حفظ');
            },
            error: function(error) {
                alert('حدث خطأ أثناء تعديل المخزون');
                console.error(error);
                
                // إعادة تفعيل زر الحفظ
                $('#save-stock-btn').prop('disabled', false).text('حفظ');
            }
        });
    }
    
    // دالة لفلترة المنتجات
    function filterProducts() {
        const categoryFilter = $('#category-filter').val();
        const stockFilter = $('#stock-filter').val();
        
        $('#products-table tbody tr').each(function() {
            const category = $(this).data('category');
            const stockStatus = $(this).data('stock-status');
            
            let categoryMatch = true;
            let stockMatch = true;
            
            if(categoryFilter && category !== categoryFilter) {
                categoryMatch = false;
            }
            
            if(stockFilter && stockStatus !== stockFilter) {
                stockMatch = false;
            }
            
            if(categoryMatch && stockMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    // دالة لتصدير المنتجات
    function exportProducts() {
        window.location.href = 'api/products/export.php';
    }
</script>