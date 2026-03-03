const $ = window.$

$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ── DataTable ──────────────────────────────────────────
    let table = $('#employeeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/employees/table',
            data: function (d) {
                d.date_from = window.filterDateFrom || '';
                d.date_to = window.filterDateTo || '';
                d.department = $('#departmentFilter').val();
            }
        },
        columns: [
            { data: 'id', searchable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'position' },
            {
                data: 'department',
                render: d => `<span class="badge bg-primary">${d}</span>`
            },
            { data: 'join_date' },
            {
                data: 'photo',
                orderable: false,
                searchable: false,
                render: d => d
                    ? `<img src="/storage/${d}" class="rounded-circle" width="36" height="36" style="object-fit:cover">`
                    : `<span class="text-muted">—</span>`
            }
        ],
        language: {
            processing: '<div class="spinner-border spinner-border-sm"></div> Loading...'
        }
    });

    // ── Column Search ─────────────────────────────────────
    $('.search-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'All'
    });

    $('.search-input, .search-select').on('click mousedown', function (e) {
        e.stopPropagation();
    });

    // Prevent sorting when clicking inside the Select2 dropdown trigger
    $(document).on('click mousedown', '.select2-container', function (e) {
        if ($(this).closest('#columnSearchRow').length) {
            e.stopPropagation();
        }
    });

    $('.search-input').on('keyup change', function () {
        let colIndex = $(this).data('column');
        table.column(colIndex).search($(this).val()).draw();
    });

    $('.search-select').on('change', function () {
        let colIndex = $(this).data('column');
        table.column(colIndex).search($(this).val()).draw();
    });

    // ── Filters ────────────────────────────────────────────
    $('#dateRangeFilter').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear', format: 'YYYY-MM-DD' }
    });

    $('#dateRangeFilter').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD MMM YYYY') + ' – ' + picker.endDate.format('DD MMM YYYY'));
        window.filterDateFrom = picker.startDate.format('YYYY-MM-DD');
        window.filterDateTo = picker.endDate.format('YYYY-MM-DD');
        table.ajax.reload();
    });

    $('#dateRangeFilter').on('cancel.daterangepicker', function () {
        $(this).val('');
        window.filterDateFrom = '';
        window.filterDateTo = '';
        table.ajax.reload();
    });

    $('#departmentFilter').on('change', () => table.ajax.reload());

    $('#resetFilter').on('click', function () {
        $('#dateRangeFilter').val('');
        $('#departmentFilter').val('').trigger('change');
        $('.search-input').val('');
        $('.search-select').val('').trigger('change');
        table.columns().search('');
        window.filterDateFrom = '';
        window.filterDateTo = '';
        table.ajax.reload();
    });

    // ── Dropzone (Photo) ──────────────────────────────────
    let photoDropzone = new Dropzone("#photoDropzone", {
        url: "/api/employees", // Dummy URL, we handle manually
        autoProcessQueue: false,
        maxFiles: 1,
        acceptedFiles: 'image/*',
        addRemoveLinks: true,
        dictDefaultMessage: "<i class='bi bi-cloud-arrow-up fs-2'></i><br>Drag and drop photo or click to upload"
    });

    photoDropzone.on("addedfile", function (file) {
        if (this.files.length > 1) {
            this.removeFile(this.files[0]);
        }
    });

    photoDropzone.on("success", function (file) {
        // Hide progress bar on successful upload
        if (file.previewElement) {
            const progressBar = file.previewElement.querySelector('.dz-progress');
            if (progressBar) {
                progressBar.style.opacity = '0';
            }
        }
    });

    // ── Select2 ───────────────────────────────────────────
    $('.select2-field').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#addModal'),
        width: '100%'
    });

    // ── Join Date picker ───────────────────────────────────
    $('#joinDate').daterangepicker({
        singleDatePicker: true,
        autoUpdateInput: false,
        locale: { format: 'YYYY-MM-DD' }
    });
    $('#joinDate').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
        $(this).removeClass('is-invalid');
    });

    // ── Validation ─────────────────────────────────────────
    function validateForm() {
        let valid = true;
        const rules = {
            name: $('#name').val().trim(),
            email: $('#email').val().trim(),
            position: $('#position').val(),
            department: $('#department').val(),
            joinDate: $('#joinDate').val().trim(),
        };
        const messages = {
            name: 'Name is required',
            email: 'Valid email is required',
            position: 'Position is required',
            department: 'Department is required',
            joinDate: 'Join date is required',
        };

        Object.keys(rules).forEach(id => $(`#${id}`).removeClass('is-invalid'));

        Object.entries(rules).forEach(([id, val]) => {
            if (!val) {
                $(`#${id}`).addClass('is-invalid')
                    .next('.invalid-feedback').text(messages[id]);
                valid = false;
            }
        });

        if ($('#email').val() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($('#email').val())) {
            $('#email').addClass('is-invalid')
                .next('.invalid-feedback').text('Enter a valid email address');
            valid = false;
        }

        return valid;
    }

    // ── Save Employee ──────────────────────────────────────
    $('#saveEmployee').on('click', function () {
        if (!validateForm()) return;

        const formData = new FormData();
        formData.append('name', $('#name').val());
        formData.append('email', $('#email').val());
        formData.append('phone', $('#phone').val());
        formData.append('position', $('#position').val());
        formData.append('department', $('#department').val());
        formData.append('join_date', $('#joinDate').val());

        // Add Photo from Dropzone
        if (photoDropzone.files.length > 0) {
            formData.append('photo', photoDropzone.files[0]);
        }

        // Add Document from standard file input
        if ($('#document')[0].files[0]) {
            formData.append('document', $('#document')[0].files[0]);
        }

        $('#loadingOverlay').css('display', 'flex');

        $.ajax({
            url: '/api/employees',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                bootstrap.Modal.getOrCreateInstance('#addModal').hide();
                table.ajax.reload();
                resetForm();
                alert('✅ ' + res.message);
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.entries(errors).forEach(([field, msgs]) => {
                        const id = field === 'join_date' ? 'joinDate' : field;
                        $(`#${id}`).addClass('is-invalid')
                            .next('.invalid-feedback').text(msgs[0]);
                    });
                }
            },
            complete: function () {
                $('#loadingOverlay').hide();
            }
        });
    });

    function resetForm() {
        ['name', 'email', 'phone', 'joinDate'].forEach(id =>
            $(`#${id}`).val('').removeClass('is-invalid'));
        ['position', 'department'].forEach(id =>
            $(`#${id}`).val('').trigger('change').removeClass('is-invalid'));

        // Reset Dropzone
        photoDropzone.removeAllFiles(true);

        // Reset standard file input
        $('#document').val('');
    }

    $('#addModal').on('hidden.bs.modal', resetForm);

});