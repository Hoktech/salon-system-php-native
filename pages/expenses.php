<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-money-bill-wave"></i> إدارة المصروفات</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" id="add-expense-btn">
            <i class="fas fa-plus"></i> إضافة مصروف جديد
        </button>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold">قائمة المصروفات</h6>
        <div class="d-flex">
            <div class="input-group input-group-sm me-2" style="width: 200px;">
                <input type="text" class="form-control" id="search-expense" placeholder="بحث...">
                <button class="btn btn-outline-secondary" type="button" id="clear-search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="dropdown me-2">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter"></i> فلترة
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown" style="min-width: 300px;">
                    <form id="filter-form">
                        <div class="mb-3 px-3">
                            <label for="category-filter" class="form-label">الفئة</label>
                            <select class="form-select form-select-sm" id="category-filter">
                                <option value="">الكل</option>
                                <!-- سيتم تحميل الفئات هنا -->
                            </select>
                        </div>
                        <div class="mb-3 px-3">
                            <label for="date-from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control form-control-sm" id="date-from">
                        </div>
                        <div class="mb-3 px-3">
                            <label for="date-to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control form-control-sm" id="date-to">
                        </div>
                        <div class="d-grid px-3 mb-2">
                            <button type="button" class="btn btn-primary btn-sm" id="apply-filter">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>
            <button class="btn btn-outline-secondary btn-sm" id="export-expenses">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="expenses-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الفئة</th>
                        <th>المبلغ</th>
                        <th>الوصف</th>
                        <th>المسجل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- سيتم تحميل البيانات هنا عبر AJAX -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">الإجمالي:</th>
                        <th id="total-amount">0</th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal إضافة/تعديل مصروف -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">إضافة مصروف جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="expense-form">
                    <input type="hidden" id="expense-id">
                    <div class="mb-3">
                        <label for="expense-category" class="form-label">الفئة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-select" id="expense-category" name="category" required>
                                <option value="">اختر الفئة</option>
                                <!-- سيتم تحميل الفئات هنا -->
                            </select>
                            <button class="btn btn-outline-secondary" type="button" id="add-category-btn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="expense-amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="expense-amount" name="amount" step="0.01" min="0" required>
                            <span class="input-group-text">ريال</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="expense-date" class="form-label">التاريخ <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="expense-date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="expense-description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="expense-description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-expense-btn">حفظ</button>
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
                    <div class="mb-3">
                        <label for="category-description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="category-description" name="description" rows="2"></textarea>
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

<script>
    $(document).ready(function() {
        // تحميل المصروفات
        loadExpenses();
        
        // تحميل فئات المصروفات
        loadExpenseCategories();
        
        // زر إضافة مصروف جديد
        $('#add-expense-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#expense-form')[0].reset();
            $('#expense-id').val('');
            $('#expense-date').val(new Date().toISOString().split('T')[0]);
            
            // تغيير عنوان النافذة المنبثقة
            $('#expenseModalLabel').text('إضافة مصروف جديد');
            
            // عرض النافذة المنبثقة
            $('#expenseModal').modal('show');
        });
        
        // زر إضافة فئة جديدة
        $('#add-category-btn').on('click', function() {
            // إعادة تعيين النموذج
            $('#category-form')[0].reset();
            
            // عرض النافذة المنبثقة
            $('#categoryModal').modal('show');
        });
        
        // زر حفظ المصروف
        $('#save-expense-btn').on('click', function() {
            saveExpense();
        });
        
        // زر حفظ الفئة
        $('#save-category-btn').on('click', function() {
            saveCategory();
        });
        
        // تطبيق الفلتر
        $('#apply-filter').on('click', function() {
            loadExpenses();
        });
        
        // البحث عن مصروف
        $('#search-expense').on('input', function() {
            const searchText = $(this).val().toLowerCase();
            
            $('#expenses-table tbody tr').each(function() {
                const category = $(this).find('td:eq(1)').text().toLowerCase();
                const description = $(this).find('td:eq(3)').text().toLowerCase();
                
                if(category.includes(searchText) || description.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // زر مسح البحث
        $('#clear-search').on('click', function() {
            $('#search-expense').val('');
            $('#expenses-table tbody tr').show();
        });
        
        // زر تصدير المصروفات
        $('#export-expenses').on('click', function() {
            exportExpenses();
        });
    });
    
    // دالة لتحميل المصروفات
    function loadExpenses() {
        // جمع قيم الفلاتر
        const filters = {
            category: $('#category-filter').val(),
            date_from: $('#date-from').val(),
            date_to: $('#date-to').val()
        };
        
        // بناء سلسلة الاستعلام
        let queryString = '';
        if(filters.category) queryString += `category=${filters.category}&`;
        if(filters.date_from) queryString += `date_from=${filters.date_from}&`;
        if(filters.date_to) queryString += `date_to=${filters.date_to}&`;
        
        // إزالة '&' الأخير إذا وجد
        if(queryString.endsWith('&')) {
            queryString = queryString.slice(0, -1);
        }
        
        // إضافة '?' إذا كان هناك استعلام
        if(queryString) {
            queryString = '?' + queryString;
        }
        
        // عرض مؤشر التحميل
        $('#expenses-table tbody').html('<tr><td colspan="6" class="text-center">جاري التحميل...</td></tr>');
        
        // طلب المصروفات من API
        $.ajax({
            url: 'api/expenses/read.php' + queryString,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const expenses = response.data;
                    
                    if(expenses.length > 0) {
                        let html = '';
                        let totalAmount = 0;
                        
                        expenses.forEach(expense => {
                            // تنسيق التاريخ
                            const date = new Date(expense.date).toLocaleDateString('ar-SA');
                            
                            // إضافة المبلغ للإجمالي
                            totalAmount += parseFloat(expense.amount);
                            
                            html += `
                                <tr>
                                    <td>${date}</td>
                                    <td>${expense.category}</td>
                                    <td>${parseFloat(expense.amount).toFixed(2)}</td>
                                    <td>${expense.description || '-'}</td>
                                    <td>${expense.user_name}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-expense" data-id="${expense.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-expense" data-id="${expense.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#expenses-table tbody').html(html);
                        $('#total-amount').text(totalAmount.toFixed(2));
                        
                        // إضافة مستمعات الأحداث
                        $('.edit-expense').on('click', function() {
                            const expenseId = $(this).data('id');
                            editExpense(expenseId);
                        });
                        
                        $('.delete-expense').on('click', function() {
                            const expenseId = $(this).data('id');
                            deleteExpense(expenseId);
                        });
                    } else {
                        $('#expenses-table tbody').html('<tr><td colspan="6" class="text-center">لا توجد مصروفات</td></tr>');
                        $('#total-amount').text('0.00');
                    }
                } else {
                    $('#expenses-table tbody').html('<tr><td colspan="6" class="text-center text-danger">حدث خطأ أثناء تحميل المصروفات</td></tr>');
                }
            },
            error: function(error) {
                $('#expenses-table tbody').html('<tr><td colspan="6" class="text-center text-danger">حدث خطأ أثناء تحميل المصروفات</td></tr>');
                console.error(error);
            }
        });
    }
    
    // دالة لتحميل فئات المصروفات
    function loadExpenseCategories() {
        $.ajax({
            url: 'api/expense_categories/read.php',
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const categories = response.data;
                    
                    if(categories.length > 0) {
                        let html = '<option value="">اختر الفئة</option>';
                        let filterHtml = '<option value="">الكل</option>';
                        
                        categories.forEach(category => {
                            html += `<option value="${category.name}">${category.name}</option>`;
                            filterHtml += `<option value="${category.name}">${category.name}</option>`;
                        });
                        
                        $('#expense-category').html(html);
                        $('#category-filter').html(filterHtml);
                    }
                }
            },
            error: function(error) {
                console.error('Error loading expense categories:', error);
            }
        });
    }
    
    // دالة لحفظ المصروف
    function saveExpense() {
        // التحقق من صحة النموذج
        if(!$('#expense-form')[0].checkValidity()) {
            $('#expense-form')[0].reportValidity();
            return;
        }
        
        // جمع بيانات المصروف
        const expenseId = $('#expense-id').val();
        const expenseData = {
            category: $('#expense-category').val(),
            amount: $('#expense-amount').val(),
            date: $('#expense-date').val(),
            description: $('#expense-description').val()
        };
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-expense-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // حفظ المصروف
        if(expenseId) {
            // تعديل مصروف موجود
            $.ajax({
                url: 'api/expenses/update.php?id=' + expenseId,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(expenseData),
                success: function(response) {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#expenseModal').modal('hide');
                        
                        // إعادة تحميل المصروفات
                        loadExpenses();
                        
                        // عرض رسالة نجاح
                        showAlert('تم تعديل المصروف بنجاح');
                    } else {
                        showAlert('حدث خطأ أثناء تعديل المصروف: ' + response.message, 'خطأ', 'danger');
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-expense-btn').prop('disabled', false).text('حفظ');
                },
                error: function(error) {
                    showAlert('حدث خطأ أثناء تعديل المصروف', 'خطأ', 'danger');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-expense-btn').prop('disabled', false).text('حفظ');
                }
            });
        } else {
            // إضافة مصروف جديد
            $.ajax({
                url: 'api/expenses/create.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(expenseData),
                success: function(response) {
                    if(response.status) {
                        // إغلاق النافذة المنبثقة
                        $('#expenseModal').modal('hide');
                        
                        // إعادة تحميل المصروفات
                        loadExpenses();
                        
                        // عرض رسالة نجاح
                        showAlert('تم إضافة المصروف بنجاح');
                    } else {
                        showAlert('حدث خطأ أثناء إضافة المصروف: ' + response.message, 'خطأ', 'danger');
                    }
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-expense-btn').prop('disabled', false).text('حفظ');
                },
                error: function(error) {
                    showAlert('حدث خطأ أثناء إضافة المصروف', 'خطأ', 'danger');
                    console.error(error);
                    
                    // إعادة تفعيل زر الحفظ
                    $('#save-expense-btn').prop('disabled', false).text('حفظ');
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
        const categoryData = {
            name: $('#category-name').val(),
            description: $('#category-description').val()
        };
        
        // تعطيل زر الحفظ أثناء المعالجة
        $('#save-category-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
        
        // حفظ الفئة
        $.ajax({
            url: 'api/expense_categories/create.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(categoryData),
            success: function(response) {
                if(response.status) {
                    // إغلاق النافذة المنبثقة
                    $('#categoryModal').modal('hide');
                    
                    // إعادة تحميل الفئات
                    loadExpenseCategories();
                    
                    // إضافة الفئة الجديدة للقائمة المنسدلة
                    $('#expense-category').append(`<option value="${categoryData.name}">${categoryData.name}</option>`);
                    $('#expense-category').val(categoryData.name);
                    
                    // عرض رسالة نجاح
                    showAlert('تم إضافة الفئة بنجاح');
                } else {
                    showAlert('حدث خطأ أثناء إضافة الفئة: ' + response.message, 'خطأ', 'danger');
                }
                
                // إعادة تفعيل زر الحفظ
                $('#save-category-btn').prop('disabled', false).text('حفظ');
            },
            error: function(error) {
                showAlert('حدث خطأ أثناء إضافة الفئة', 'خطأ', 'danger');
                console.error(error);
                
                // إعادة تفعيل زر الحفظ
                $('#save-category-btn').prop('disabled', false).text('حفظ');
            }
        });
    }
    
    // دالة لتعديل المصروف
    function editExpense(expenseId) {
        $.ajax({
            url: 'api/expenses/read_one.php?id=' + expenseId,
            type: 'GET',
            success: function(response) {
                if(response.status) {
                    const expense = response.data;
                    
                    // تعبئة النموذج ببيانات المصروف
                    $('#expense-id').val(expense.id);
                    $('#expense-category').val(expense.category);
                    $('#expense-amount').val(expense.amount);
                    $('#expense-date').val(expense.date);
                    $('#expense-description').val(expense.description);
                    
                    // تغيير عنوان النافذة المنبثقة
                    $('#expenseModalLabel').text('تعديل المصروف');
                    
                    // عرض النافذة المنبثقة
                    $('#expenseModal').modal('show');
                } else {
                    showAlert('حدث خطأ أثناء تحميل بيانات المصروف: ' + response.message, 'خطأ', 'danger');
                }
            },
            error: function(error) {
                showAlert('حدث خطأ أثناء تحميل بيانات المصروف', 'خطأ', 'danger');
                console.error(error);
            }
        });
    }
    
    // دالة لحذف المصروف
    function deleteExpense(expenseId) {
        if(confirm('هل أنت متأكد من حذف هذا المصروف؟')) {
            $.ajax({
                url: 'api/expenses/delete.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: expenseId }),
                success: function(response) {
                    if(response.status) {
                        // إعادة تحميل المصروفات
                        loadExpenses();
                        
                        // عرض رسالة نجاح
                        showAlert('تم حذف المصروف بنجاح');
                    } else {
                        showAlert('حدث خطأ أثناء حذف المصروف: ' + response.message, 'خطأ', 'danger');
                    }
                },
                error: function(error) {
                    showAlert('حدث خطأ أثناء حذف المصروف', 'خطأ', 'danger');
                    console.error(error);
                }
            });
        }
    }
    
    // دالة لتصدير المصروفات
    function exportExpenses() {
        // جمع قيم الفلاتر
        const filters = {
            category: $('#category-filter').val(),
            date_from: $('#date-from').val(),
            date_to: $('#date-to').val()
        };
        
        // بناء سلسلة الاستعلام
        let queryString = '?export=1';
        if(filters.category) queryString += `&category=${filters.category}`;
        if(filters.date_from) queryString += `&date_from=${filters.date_from}`;
        if(filters.date_to) queryString += `&date_to=${filters.date_to}`;
        
        // الانتقال إلى رابط التصدير
        window.location.href = 'api/expenses/export.php' + queryString;
    }
</script>