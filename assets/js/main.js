/**
 * الملف الرئيسي لـ JavaScript - نظام إدارة صالونات الحلاقة والكوافير
 */

// كائن لتخزين البيانات الشائعة الاستخدام
const appData = {
    chartInstances: {}, // لتخزين مراجع الرسوم البيانية
    currentPage: '', // الصفحة الحالية
    userData: {} // بيانات المستخدم الحالي
};

// عند تحميل المستند
$(document).ready(function() {
    // تهيئة التطبيق
    initializeApp();
    
    // مستمعات الأحداث العامة
    setupEventListeners();
});

/**
 * تهيئة التطبيق عند التحميل
 */
function initializeApp() {
    // تحميل بيانات المستخدم
    loadUserData();
    
    // تنشيط عنصر القائمة الحالي
    const currentPath = window.location.pathname;
    if (currentPath.endsWith('/') || currentPath.endsWith('/index.php')) {
        // إذا كان في الصفحة الرئيسية، قم بتحميل لوحة التحكم
        $('.nav-link[data-page="dashboard"]').addClass('active');
        loadPage('dashboard');
    }
}

/**
 * إعداد مستمعات الأحداث العامة
 */
function setupEventListeners() {
    // مستمع النقر على عناصر القائمة
    $('.nav-link').on('click', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        
        if (page) {
            loadPage(page);
        }
    });
    
    // مستمع زر تبديل القائمة الجانبية
    $('#menu-toggle').on('click', function(e) {
        e.preventDefault();
        $('#wrapper').toggleClass('toggled');
    });
    
    // معالجة النماذج العامة
    $(document).on('submit', 'form.ajax-form', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action') || 'api/' + form.data('endpoint');
        const method = form.attr('method') || 'POST';
        const formData = new FormData(this);
        
        submitForm(form, url, method, formData);
    });
    
    // زر تسجيل الخروج
    $('#logout-btn').on('click', function(e) {
        e.preventDefault();
        logout();
    });
}

/**
 * تحميل بيانات المستخدم الحالي
 */
function loadUserData() {
    // لاحقًا يمكن أن نقوم بتحميل المزيد من بيانات المستخدم من الخادم إذا لزم الأمر
    appData.userData = {
        id: userInfo.id,
        name: userInfo.name,
        role: userInfo.role,
        permissions: userInfo.permissions || []
    };
}

/**
 * دالة لتحميل صفحة معينة
 * 
 * @param {string} page اسم الصفحة المراد تحميلها
 */
function loadPage(page) {
    // حفظ الصفحة الحالية
    appData.currentPage = page;
    
    // تحميل الصفحة باستخدام AJAX
    $.ajax({
        url: 'pages/' + page + '.php',
        type: 'GET',
        beforeSend: function() {
            $('#main-content').html(`
                <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            `);
        },
        success: function(data) {
            $('#main-content').html(data);
            
            // تنشيط الرابط الحالي في القائمة
            $('.nav-link').removeClass('active');
            $('.nav-link[data-page="' + page + '"]').addClass('active');
            
            // تحديث عنوان الصفحة
            const pageTitle = $('.nav-link[data-page="' + page + '"]').text().trim();
            updatePageTitle(pageTitle);
            
            // إغلاق القائمة الجانبية في الأجهزة الصغيرة بعد اختيار صفحة
            if ($(window).width() < 768) {
                $('#wrapper').addClass('toggled');
            }
            
            // تدمير أي رسم بياني موجود (لتجنب تسريبات الذاكرة)
            destroyCharts();
            
            // تشغيل الكود الخاص بالصفحة إذا وجد
            if (typeof window[page + 'PageInit'] === 'function') {
                window[page + 'PageInit']();
            }
        },
        error: function(xhr, status, error) {
            $('#main-content').html(`
                <div class="alert alert-danger">
                    <h4 class="alert-heading">حدث خطأ أثناء تحميل الصفحة</h4>
                    <p>${error}</p>
                    <button class="btn btn-outline-danger" onclick="loadPage('${page}')">
                        <i class="fas fa-sync-alt"></i> إعادة المحاولة
                    </button>
                </div>
            `);
        }
    });
}

/**
 * تدمير جميع الرسومات البيانية لتفادي مشكلات الذاكرة
 */
function destroyCharts() {
    for (const chartId in appData.chartInstances) {
        if (appData.chartInstances[chartId]) {
            appData.chartInstances[chartId].destroy();
            delete appData.chartInstances[chartId];
        }
    }
}

/**
 * تحديث عنوان الصفحة
 * 
 * @param {string} title عنوان الصفحة
 */
function updatePageTitle(title) {
    document.title = title + ' - نظام إدارة صالونات الحلاقة والكوافير';
}

/**
 * تسجيل الخروج من النظام
 */
function logout() {
    if (confirm('هل أنت متأكد من رغبتك في تسجيل الخروج؟')) {
        $.ajax({
            url: 'api/auth/logout.php',
            type: 'POST',
            success: function(response) {
                if (response.status) {
                    window.location.href = 'login.php';
                } else {
                    alert('حدث خطأ أثناء تسجيل الخروج: ' + response.message);
                }
            },
            error: function() {
                alert('حدث خطأ في الاتصال بالخادم');
            }
        });
    }
}

/**
 * تقديم نموذج باستخدام AJAX
 * 
 * @param {jQuery} form عنصر النموذج
 * @param {string} url عنوان الإرسال
 * @param {string} method طريقة الإرسال (POST/GET)
 * @param {FormData} formData بيانات النموذج
 */
function submitForm(form, url, method, formData) {
    // تعطيل زر الإرسال
    const submitButton = form.find('button[type="submit"]');
    const originalText = submitButton.html();
    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري المعالجة...');
    
    // تحويل FormData إلى كائن JSON إذا لم تكن هناك ملفات
    let processedData = formData;
    let contentType;
    
    // التحقق من وجود ملفات في النموذج
    let hasFiles = false;
    for (const pair of formData.entries()) {
        if (pair[1] instanceof File && pair[1].name !== '') {
            hasFiles = true;
            break;
        }
    }
    
    if (!hasFiles) {
        // تحويل البيانات إلى كائن JSON
        const jsonData = {};
        for (const pair of formData.entries()) {
            jsonData[pair[0]] = pair[1];
        }
        processedData = JSON.stringify(jsonData);
        contentType = 'application/json';
    } else {
        // استخدام FormData كما هي للنماذج التي تحتوي على ملفات
        contentType = false;
    }
    
    // إرسال الطلب
    $.ajax({
        url: url,
        type: method,
        data: processedData,
        contentType: contentType,
        processData: false, // ضروري لـ FormData
        success: function(response) {
            // إعادة تمكين زر الإرسال
            submitButton.prop('disabled', false).html(originalText);
            
            // التعامل مع رد الخادم
            if (response.status) {
                // إذا كان هناك دالة معالجة النجاح محددة
                if (typeof form.data('success') === 'function') {
                    form.data('success')(response);
                } else {
                    // عرض رسالة النجاح
                    showAlert(form.find('.form-alert'), 'success', response.message);
                    
                    // إعادة تعيين النموذج إذا كان مطلوبًا
                    if (form.data('reset') !== false) {
                        form[0].reset();
                    }
                    
                    // تحديث الصفحة أو إعادة التوجيه إذا كان محدداً
                    if (form.data('redirect')) {
                        setTimeout(function() {
                            if (form.data('redirect') === 'refresh') {
                                location.reload();
                            } else {
                                window.location.href = form.data('redirect');
                            }
                        }, 1000);
                    } else if (form.data('reload')) {
                        setTimeout(function() {
                            loadPage(appData.currentPage);
                        }, 1000);
                    }
                }
            } else {
                // عرض رسالة الخطأ
                showAlert(form.find('.form-alert'), 'danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            // إعادة تمكين زر الإرسال
            submitButton.prop('disabled', false).html(originalText);
            
            // عرض رسالة الخطأ
            showAlert(form.find('.form-alert'), 'danger', 'حدث خطأ في الاتصال بالخادم: ' + error);
        }
    });
}

/**
 * عرض رسالة تنبيه
 * 
 * @param {jQuery} container حاوية التنبيه
 * @param {string} type نوع التنبيه (success/danger/warning/info)
 * @param {string} message نص الرسالة
 * @param {boolean} autoHide إخفاء تلقائي بعد فترة
 */
function showAlert(container, type, message, autoHide = true) {
    const alertClass = 'alert-' + type;
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    container.html(alertHtml);
    
    if (autoHide) {
        setTimeout(function() {
            container.find('.alert').alert('close');
        }, 5000);
    }
}

/**
 * متغير عام لتخزين معلومات المستخدم
 * سيتم تعبئته بالبيانات من خلال PHP
 */
