/**
 * ملف يحتوي على دالات للتواصل مع واجهة برمجة التطبيقات
 */

// الأساس URL للـ API
const API_BASE_URL = 'api/';

// دالة للحصول على التوكن من الجلسة
function getAuthToken() {
    // في هذا المثال، نعتمد على الجلسة للمصادقة
    return null;
}

// دالة لإرسال طلب إلى API
function apiRequest(endpoint, method = 'GET', data = null) {
    // إعداد الخيارات
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    // إضافة التوكن إذا كان متاحاً
    const token = getAuthToken();
    if(token) {
        options.headers['Authorization'] = 'Bearer ' + token;
    }
    
    // إضافة البيانات للطلبات POST و PUT
    if(data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }
    
    // إرسال الطلب
    return new Promise((resolve, reject) => {
        fetch(API_BASE_URL + endpoint, options)
            .then(response => {
                // التحقق من الاستجابة
                if(!response.ok) {
                    // في حالة الخطأ 401، إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
                    if(response.status === 401) {
                        window.location.href = 'login.php';
                        return;
                    }
                    
                    // معالجة الأخطاء الأخرى
                    return response.json().then(err => {
                        throw new Error(err.message || 'حدث خطأ غير معروف');
                    });
                }
                
                // تحويل الاستجابة إلى JSON
                return response.json();
            })
            .then(data => {
                resolve(data);
            })
            .catch(error => {
                reject(error);
            });
    });
}

/**
 * دالات المستخدمين
 */

// دالة تسجيل الدخول
function login(username, password) {
    return apiRequest('auth/login.php', 'POST', { username, password });
}

// دالة تسجيل الخروج
function logout() {
    return apiRequest('auth/logout.php', 'POST');
}

/**
 * دالات العملاء
 */

// دالة للحصول على قائمة العملاء
function getCustomers() {
    return apiRequest('customers/read.php');
}

// دالة للحصول على عميل محدد
function getCustomer(id) {
    return apiRequest('customers/read_one.php?id=' + id);
}

// دالة لإضافة عميل جديد
function addCustomer(customerData) {
    return apiRequest('customers/create.php', 'POST', customerData);
}

// دالة لتعديل عميل
function updateCustomer(id, customerData) {
    return apiRequest('customers/update.php?id=' + id, 'POST', customerData);
}

// دالة لحذف عميل
function deleteCustomer(id) {
    return apiRequest('customers/delete.php?id=' + id, 'POST');
}

/**
 * دالات الخدمات
 */

// دالة للحصول على قائمة الخدمات
function getServices() {
    return apiRequest('services/read.php');
}

// دالة للحصول على خدمة محددة
function getService(id) {
    return apiRequest('services/read_one.php?id=' + id);
}

// دالة لإضافة خدمة جديدة
function addService(serviceData) {
    return apiRequest('services/create.php', 'POST', serviceData);
}

// دالة لتعديل خدمة
function updateService(id, serviceData) {
    return apiRequest('services/update.php?id=' + id, 'POST', serviceData);
}

// دالة لحذف خدمة
function deleteService(id) {
    return apiRequest('services/delete.php?id=' + id, 'POST');
}

/**
 * دالات المنتجات
 */

// دالة للحصول على قائمة المنتجات
function getProducts() {
    return apiRequest('products/read.php');
}

// دالة للحصول على منتج محدد
function getProduct(id) {
    return apiRequest('products/read_one.php?id=' + id);
}

// دالة لإضافة منتج جديد
function addProduct(productData) {
    return apiRequest('products/create.php', 'POST', productData);
}

// دالة لتعديل منتج
function updateProduct(id, productData) {
    return apiRequest('products/update.php?id=' + id, 'POST', productData);
}

// دالة لحذف منتج
function deleteProduct(id) {
    return apiRequest('products/delete.php?id=' + id, 'POST');
}

/**
 * دالات المواعيد
 */

// دالة للحصول على قائمة المواعيد
function getAppointments(date = null) {
    let endpoint = 'appointments/read.php';
    if(date) {
        endpoint += '?date=' + date;
    }
    return apiRequest(endpoint);
}

// دالة للحصول على موعد محدد
function getAppointment(id) {
    return apiRequest('appointments/read_one.php?id=' + id);
}

// دالة لإضافة موعد جديد
function addAppointment(appointmentData) {
    return apiRequest('appointments/create.php', 'POST', appointmentData);
}

// دالة لتعديل موعد
function updateAppointment(id, appointmentData) {
    return apiRequest('appointments/update.php?id=' + id, 'POST', appointmentData);
}

// دالة لحذف موعد
function deleteAppointment(id) {
    return apiRequest('appointments/delete.php?id=' + id, 'POST');
}

/**
 * دالات الفواتير
 */

// دالة للحصول على قائمة الفواتير
function getInvoices(filters = {}) {
    let endpoint = 'invoices/read.php';
    const queryParams = [];
    
    if(filters.start_date) {
        queryParams.push('start_date=' + filters.start_date);
    }
    if(filters.end_date) {
        queryParams.push('end_date=' + filters.end_date);
    }
    if(filters.customer_id) {
        queryParams.push('customer_id=' + filters.customer_id);
    }
    if(filters.payment_method) {
        queryParams.push('payment_method=' + filters.payment_method);
    }
    
    if(queryParams.length > 0) {
        endpoint += '?' + queryParams.join('&');
    }
    
    return apiRequest(endpoint);
}

// دالة للحصول على فاتورة محددة
function getInvoice(id) {
    return apiRequest('invoices/read_one.php?id=' + id);
}

// دالة لإنشاء فاتورة جديدة
function createInvoice(invoiceData) {
    return apiRequest('invoices/create.php', 'POST', invoiceData);
}

// دالة لطباعة فاتورة
function printInvoice(id) {
    return apiRequest('invoices/print.php?id=' + id);
}

/**
 * دالات المصروفات
 */

// دالة للحصول على قائمة المصروفات
function getExpenses(filters = {}) {
    let endpoint = 'expenses/read.php';
    const queryParams = [];
    
    if(filters.start_date) {
        queryParams.push('start_date=' + filters.start_date);
    }
    if(filters.end_date) {
        queryParams.push('end_date=' + filters.end_date);
    }
    if(filters.category) {
        queryParams.push('category=' + filters.category);
    }
    
    if(queryParams.length > 0) {
        endpoint += '?' + queryParams.join('&');
    }
    
    return apiRequest(endpoint);
}

// دالة لإضافة مصروف جديد
function addExpense(expenseData) {
    return apiRequest('expenses/create.php', 'POST', expenseData);
}

/**
 * دالات الموظفين
 */

// دالة للحصول على قائمة الموظفين
function getEmployees() {
    return apiRequest('employees/read.php');
}

// دالة للحصول على موظف محدد
function getEmployee(id) {
    return apiRequest('employees/read_one.php?id=' + id);
}

// دالة لإضافة موظف جديد
function addEmployee(employeeData) {
    return apiRequest('employees/create.php', 'POST', employeeData);
}

// دالة لتعديل موظف
function updateEmployee(id, employeeData) {
    return apiRequest('employees/update.php?id=' + id, 'POST', employeeData);
}

// دالة لحذف موظف
function deleteEmployee(id) {
    return apiRequest('employees/delete.php?id=' + id, 'POST');
}

/**
 * دالات الفروع
 */

// دالة للحصول على قائمة الفروع
function getBranches() {
    return apiRequest('branches/read.php');
}

// دالة للحصول على فرع محدد
function getBranch(id) {
    return apiRequest('branches/read_one.php?id=' + id);
}

// دالة لإضافة فرع جديد
function addBranch(branchData) {
    return apiRequest('branches/create.php', 'POST', branchData);
}

// دالة لتعديل فرع
function updateBranch(id, branchData) {
    return apiRequest('branches/update.php?id=' + id, 'POST', branchData);
}

// دالة لحذف فرع
function deleteBranch(id) {
    return apiRequest('branches/delete.php?id=' + id, 'POST');
}

/**
 * دالات التقارير
 */

// دالة للحصول على تقرير المبيعات
function getSalesReport(filters = {}) {
    let endpoint = 'reports/sales.php';
    const queryParams = [];
    
    if(filters.start_date) {
        queryParams.push('start_date=' + filters.start_date);
    }
    if(filters.end_date) {
        queryParams.push('end_date=' + filters.end_date);
    }
    if(filters.branch_id) {
        queryParams.push('branch_id=' + filters.branch_id);
    }
    if(filters.payment_method) {
        queryParams.push('payment_method=' + filters.payment_method);
    }
    
    if(queryParams.length > 0) {
        endpoint += '?' + queryParams.join('&');
    }
    
    return apiRequest(endpoint);
}

// دالة للحصول على تقرير المصروفات
function getExpensesReport(filters = {}) {
    let endpoint = 'reports/expenses.php';
    const queryParams = [];
    
    if(filters.start_date) {
        queryParams.push('start_date=' + filters.start_date);
    }
    if(filters.end_date) {
        queryParams.push('end_date=' + filters.end_date);
    }
    if(filters.branch_id) {
        queryParams.push('branch_id=' + filters.branch_id);
    }
    if(filters.category) {
        queryParams.push('category=' + filters.category);
    }
    
    if(queryParams.length > 0) {
        endpoint += '?' + queryParams.join('&');
    }
    
    return apiRequest(endpoint);
}

// دالة للحصول على تقرير أداء الموظفين
function getEmployeesReport(filters = {}) {
    let endpoint = 'reports/employees.php';
    const queryParams = [];
    
    if(filters.start_date) {
        queryParams.push('start_date=' + filters.start_date);
    }
    if(filters.end_date) {
        queryParams.push('end_date=' + filters.end_date);
    }
    if(filters.branch_id) {
        queryParams.push('branch_id=' + filters.branch_id);
    }
    if(filters.employee_id) {
        queryParams.push('employee_id=' + filters.employee_id);
    }
    
    if(queryParams.length > 0) {
        endpoint += '?' + queryParams.join('&');
    }
    
    return apiRequest(endpoint);
}

// دالة للحصول على تقرير العملاء
function getCustomersReport(filters = {}) {
    let endpoint = 'reports/customers.php';
    const queryParams = [];
    
    if(filters.start_date) {
        queryParams.push('start_date=' + filters.start_date);
    }
    if(filters.end_date) {
        queryParams.push('end_date=' + filters.end_date);
    }
    if(filters.branch_id) {
        queryParams.push('branch_id=' + filters.branch_id);
    }
    
    if(queryParams.length > 0) {
        endpoint += '?' + queryParams.join('&');
    }
    
    return apiRequest(endpoint);
}

/**
 * دالات إغلاق اليوم
 */

// دالة لإغلاق اليوم
function closeDay(notes = '') {
    return apiRequest('day_end/close.php', 'POST', { notes });
}