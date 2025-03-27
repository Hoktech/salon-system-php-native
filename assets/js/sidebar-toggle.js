/**
 * ملف التحكم في القائمة الجانبية
 */
 
document.addEventListener('DOMContentLoaded', function() {
    // زر تبديل القائمة الجانبية
    const menuToggle = document.getElementById('menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const wrapper = document.getElementById('wrapper');
            wrapper.classList.toggle('toggled');
        });
    }
    
    // في الشاشات الصغيرة، نخفي القائمة عند النقر على أي رابط
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                const wrapper = document.getElementById('wrapper');
                wrapper.classList.add('toggled');
            }
        });
    });
    
    // إضافة الصنف النشط للرابط الحالي
    const currentPage = getCurrentPage();
    const activeLink = document.querySelector(`.sidebar-nav a[data-page="${currentPage}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
});

/**
 * الحصول على اسم الصفحة الحالية من عنوان URL
 */
function getCurrentPage() {
    // إذا كانت الصفحة الرئيسية أو index.php
    const path = window.location.pathname;
    if (path.endsWith('/') || path.endsWith('/index.php')) {
        return 'dashboard';
    }
    
    // استخراج اسم الصفحة من URL إذا كان بتنسيق page=xxx
    const urlParams = new URLSearchParams(window.location.search);
    const pageParam = urlParams.get('page');
    if (pageParam) {
        return pageParam;
    }
    
    // في حال كان هناك تنسيق آخر للروابط
    const pathSegments = path.split('/').filter(Boolean);
    const lastSegment = pathSegments[pathSegments.length - 1];
    if (lastSegment && lastSegment.endsWith('.php')) {
        return lastSegment.replace('.php', '');
    }
    
    return 'dashboard'; // افتراضي
}