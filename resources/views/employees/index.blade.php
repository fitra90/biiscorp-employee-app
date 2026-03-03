<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Management</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/employees.js'])

    <style>
        body {
            background: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-add {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
        }

        .btn-add:hover {
            opacity: 0.9;
            color: white;
        }

        .badge-dept {
            font-size: 0.75rem;
        }

        #loadingOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <div id="loadingOverlay">
        <div class="spinner-border text-light" style="width:3rem;height:3rem;"></div>
    </div>

    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Employee Management</h5>
                <button class="btn btn-add px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                    + Add Employee
                </button>
            </div>
            <div class="card-body">

                {{-- Filters --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filter by Join Date</label>
                        <input type="text" id="dateRangeFilter" class="form-control" placeholder="Select date range">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filter by Department</label>
                        <select id="departmentFilter" class="form-select">
                            <option value="">All Departments</option>
                            <option>Engineering</option>
                            <option>Marketing</option>
                            <option>HR</option>
                            <option>Finance</option>
                            <option>Operations</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="resetFilter" class="btn btn-outline-secondary w-100">Reset Filters</button>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="table-responsive">
                    <table id="employeeTable" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Join Date</th>
                                <th>Photo</th>
                            </tr>
                            <tr id="columnSearchRow">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm search-input" data-column="1"
                                        placeholder="Search Name"></th>
                                <th><input type="text" class="form-control form-control-sm search-input" data-column="2"
                                        placeholder="Search Email"></th>
                                <th>
                                    <select class="form-select form-select-sm search-select" data-column="3">
                                        <option value="">All</option>
                                        <option>Manager</option>
                                        <option>Staff</option>
                                        <option>Senior Staff</option>
                                        <option>Supervisor</option>
                                        <option>Director</option>
                                    </select>
                                </th>
                                <th>
                                    <select class="form-select form-select-sm search-select" data-column="4">
                                        <option value="">All</option>
                                        <option>Engineering</option>
                                        <option>Marketing</option>
                                        <option>HR</option>
                                        <option>Finance</option>
                                        <option>Operations</option>
                                    </select>
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Employee Modal --}}
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg,#667eea,#764ba2); color:white;">
                    <h5 class="modal-title">Add New Employee</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="formErrors" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" class="form-control" placeholder="Enter full name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" class="form-control" placeholder="email@company.com">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" id="phone" class="form-control" placeholder="+62...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Join Date <span class="text-danger">*</span></label>
                            <input type="text" id="joinDate" class="form-control" placeholder="Pick a date">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Position <span class="text-danger">*</span></label>
                            <select id="position" class="form-select select2-field">
                                <option value="">Select position...</option>
                                <option>Staff</option>
                                <option>Senior Staff</option>
                                <option>Supervisor</option>
                                <option>Manager</option>
                                <option>Director</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <select id="department" class="form-select select2-field">
                                <option value="">Select department...</option>
                                <option>Engineering</option>
                                <option>Marketing</option>
                                <option>HR</option>
                                <option>Finance</option>
                                <option>Operations</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Photo</label>
                            <div id="photoDropzone" class="dropzone">
                                <div class="dz-message">
                                    <i class="bi bi-cloud-arrow-up fs-2"></i><br>
                                    Drag and drop photo or click to upload
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Document</label>
                            <input type="file" id="document" name="document" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveEmployee" class="btn btn-add px-4">Save Employee</button>
                </div>
            </div>
        </div>
    </div>


</body>

</html>