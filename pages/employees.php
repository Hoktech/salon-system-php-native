<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-user-tie"></i> إدارة الموظفين</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" id="add-employee-btn">
            <i class="fas fa-plus"></i> إضافة موظف جديد
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold">قائمة الموظفين</h6>
                <div class="d-flex">
                    <div class="input-group input-group-sm me-2" style="width: 200px;">
                        <input type="text" class="form-control" id="search-employee" placeholder="بحث...">
                        <button class="btn btn-outline-secondary" type="button" id="clear-search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter"></i> فلترة
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            <h6 class="dropdown-header">الدور</h6>
                            <div class="px-3 py-1">
                                <select class="form-select form-select-sm" id="role-filter">
                                    <option value="">الكل</option>
                                    