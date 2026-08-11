@extends('layouts.app')

@section('content')
@php
    $counts = [
        'all' => $registrations->count(),
        'pending' => $registrations->where('status', 'pending')->count(),
        'revision' => $registrations->where('status', 'revision')->count(),
        'approved' => $registrations->where('status', 'approved')->count(),
        'rejected' => $registrations->where('status', 'rejected')->count(),
    ];
@endphp
<style>
    .reg-stat { background:#fff; border:1px solid #e3e8ef; border-radius:8px; padding:12px 14px; margin-bottom:12px; cursor:pointer; height:100%; }
    .reg-stat:hover, .reg-stat.active { border-color:#1a73a7; box-shadow:0 2px 8px rgba(26,115,167,.12); }
    .reg-stat .n { font-size:1.5rem; font-weight:700; color:#1a3a52; }
    .reg-stat .l { font-size:.8rem; color:#6b7c8c; text-transform:uppercase; }
    .reg-stat.pending .n { color:#b78105; }
    .reg-stat.revision .n { color:#0c6e9e; }
    .reg-stat.approved .n { color:#1e7e34; }
    .reg-stat.rejected .n { color:#c0392b; }
    .status-pill { display:inline-block; padding:3px 10px; border-radius:999px; font-size:.75rem; font-weight:600; }
    .status-pill.pending { background:#fff3cd; color:#856404; }
    .status-pill.revision { background:#d1ecf1; color:#0c5460; }
    .status-pill.approved { background:#d4edda; color:#155724; }
    .status-pill.rejected { background:#f8d7da; color:#721c24; }
    .reg-actions { display:flex; flex-wrap:wrap; gap:6px; }
    .reg-actions .btn { font-size:.78rem; padding:4px 10px; }
    .edit-link-box { display:flex; gap:8px; align-items:stretch; margin-top:8px; }
    .edit-link-box input { flex:1; font-size:12px; font-family:monospace; }
    .view-section { border:1px solid #e8eef4; border-radius:8px; padding:12px; margin-bottom:12px; }
    .view-section h6 { font-size:.8rem; text-transform:uppercase; color:#6b7c8c; margin-bottom:8px; }
    .view-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .view-label { display:block; font-size:.72rem; color:#6b7c8c; }
    .view-val { font-weight:600; color:#1a3a52; }
    .view-photo { width:84px; height:84px; object-fit:cover; border-radius:8px; border:1px solid #ddd; background:#f5f5f5; }
    .doc-chip {
        display: inline-block;
        margin: 0 8px 8px 0;
        padding: 6px 12px;
        border: 1px solid #c5d6e4;
        border-radius: 6px;
        background: #f7fbfe;
        color: #1a3a52;
        font-size: .82rem;
        text-decoration: none;
    }
    .doc-chip:hover { background: #eef6fb; }
</style>

<div class="container-fluid mt-2">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:10px;">
        <div>
            <h2 class="heading mb-1">District Coordinator Registrations</h2>
            <div class="text-muted small">Public applications for <strong>district coordinators only</strong> (state coordinators are admin-created).</div>
        </div>
        <div class="reg-link-box mb-0" style="background:#f4f8fb;border:1px solid #d7e4ef;border-radius:8px;padding:10px 14px;">
            <span class="small text-muted">Public form:</span>
            <a href="{{ route('coordinator.register') }}" target="_blank">{{ route('coordinator.register') }}</a>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-copy-public-link" data-url="{{ route('coordinator.register') }}">Copy</button>
        </div>
    </div>

    <div class="row mb-3">
        @foreach(['all'=>'All','pending'=>'Pending','revision'=>'Correction','approved'=>'Approved','rejected'=>'Rejected'] as $key => $label)
            <div class="col-6 col-md">
                <div class="reg-stat {{ $key === 'all' ? '' : $key }} {{ $key === 'all' ? 'active' : '' }}" data-filter="{{ $key }}">
                    <div class="n">{{ $counts[$key] }}</div>
                    <div class="l">{{ $label }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card-body p-0" style="background:#fff;border:1px solid #e3e8ef;border-radius:8px;padding:12px !important;">
        <table class="table table-bordered table-sm" id="coordRegTable" style="width:100%;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>District / Block</th>
                    <th>Applied</th>
                    <th>Status</th>
                    <th style="min-width:200px;">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($registrations as $row)
                <tr data-status="{{ $row->status }}">
                    <td>
                        <div class="font-weight-bold">{{ $row->instructor_name }}</div>
                        <div class="small text-muted">{{ $row->instructor_code ?? '—' }}</div>
                    </td>
                    <td>
                        <div>{{ $row->email }}</div>
                        <div class="small text-muted">{{ $row->instructor_number }}</div>
                    </td>
                    <td>
                        <div>{{ $row->district }}</div>
                        <div class="small text-muted">{{ $row->block }}</div>
                    </td>
                    <td data-order="{{ $row->created_at?->timestamp }}">
                        {{ $row->created_at?->format('d-m-Y') }}<br>
                        <small class="text-muted">{{ $row->created_at?->format('H:i') }}</small>
                    </td>
                    <td>
                        @if($row->status === 'pending')
                            <span class="status-pill pending">Pending</span>
                        @elseif($row->status === 'revision')
                            <span class="status-pill revision">Awaiting Correction</span>
                            @if($row->admin_remarks)
                                <div class="small text-muted mt-1" title="{{ $row->admin_remarks }}">{{ \Illuminate\Support\Str::limit($row->admin_remarks, 36) }}</div>
                            @endif
                        @elseif($row->status === 'approved')
                            <span class="status-pill approved">Approved</span>
                        @else
                            <span class="status-pill rejected">Rejected</span>
                        @endif
                    </td>
                    <td>
                        <div class="reg-actions">
                            <button type="button" class="btn btn-secondary btn-view" data-id="{{ $row->id }}">View</button>
                            @if($row->status === 'pending')
                                <button type="button" class="btn btn-success btn-approve"
                                    data-id="{{ $row->id }}"
                                    data-name="{{ $row->instructor_name }}"
                                    data-email="{{ $row->email }}"
                                    data-code="{{ $row->instructor_code }}">
                                    Approve
                                </button>
                                <button type="button" class="btn btn-warning btn-revision" data-id="{{ $row->id }}">Correction</button>
                                <button type="button" class="btn btn-danger btn-reject" data-id="{{ $row->id }}">Reject</button>
                            @elseif($row->status === 'revision')
                                <button type="button" class="btn btn-danger btn-reject" data-id="{{ $row->id }}">Reject</button>
                            @elseif($row->status === 'approved' && $row->user_id)
                                <a href="{{ url('getData/'.$row->user_id) }}" class="btn btn-info">Open Profile</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Approve modal --}}
<div class="modal fade" id="approve-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Approve District Coordinator</h4>
                <button type="button" class="close modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Approve <strong id="approve-name"></strong> <span id="approve-email" class="text-muted"></span> as District Coordinator.</p>
                <form id="approveForm">
                    <input type="hidden" id="registration_id">
                    <div class="form-group">
                        <label>Password (sent by email)</label>
                        <input type="text" class="form-control" name="password" id="approve_password" value="SOPL@1634">
                    </div>
                    <div class="small text-muted">Level is fixed to <strong>District</strong>. Code: <span id="approve_code_label"></span></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btn-approve-save">Approve &amp; Send Email</button>
            </div>
        </div>
    </div>
</div>

{{-- View modal --}}
<div class="modal fade" id="view-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Registration Details</h4>
                <button type="button" class="close modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="view-modal-body">Loading...</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
function showBsModal(selector) {
    const el = document.querySelector(selector);
    if (!el) return;
    try {
        if (window.bootstrap && bootstrap.Modal) {
            (bootstrap.Modal.getInstance(el) || bootstrap.Modal.getOrCreateInstance(el)).show();
            return;
        }
    } catch (e) {}
    if (window.jQuery && typeof jQuery(el).modal === 'function') {
        jQuery(el).modal('show');
    }
}
function hideBsModal(selector) {
    const el = document.querySelector(selector);
    if (!el) return;
    try {
        if (window.bootstrap && bootstrap.Modal) {
            const inst = bootstrap.Modal.getInstance(el) || bootstrap.Modal.getOrCreateInstance(el);
            inst.hide();
            return;
        }
    } catch (e) {}
    if (window.jQuery && typeof jQuery(el).modal === 'function') {
        jQuery(el).modal('hide');
    }
}
function esc(v) {
    return String(v == null ? '' : v)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
function mediaFileUrl(path) {
    if (!path) return '';
    const name = String(path).split('/').pop();
    return '{{ url('/m/o') }}/' + encodeURIComponent(name);
}
function docChip(label, path) {
    const url = mediaFileUrl(path);
    if (!url) return `<span class="text-muted small me-2">${label}: —</span>`;
    return `<a class="doc-chip" href="${url}" target="_blank" rel="noopener">${label}</a>`;
}
function copyTextToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    const ta = document.createElement('textarea');
    ta.value = text;
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); return Promise.resolve(); }
    finally { document.body.removeChild(ta); }
}
function editLinkSection(editUrl) {
    if (!editUrl) return '';
    const safeUrl = esc(editUrl);
    return `
        <div class="view-section">
            <h6>Applicant Edit Link</h6>
            <div class="small text-muted mb-1">Same link emailed for correction — copy if mail did not reach.</div>
            <div class="edit-link-box">
                <input type="text" class="form-control form-control-sm" readonly value="${safeUrl}" id="coord-edit-link-input">
                <button type="button" class="btn btn-sm btn-outline-primary btn-copy-edit-link" data-url="${safeUrl}">Copy</button>
                <a href="${safeUrl}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Open</a>
            </div>
        </div>`;
}

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const table = $('#coordRegTable').DataTable({ order: [[3, 'desc']], pageLength: 25 });

$('.reg-stat').on('click', function () {
    $('.reg-stat').removeClass('active');
    $(this).addClass('active');
    const filter = $(this).data('filter');
    $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(fn => !fn._coordStatusFilter);
    if (filter !== 'all') {
        const fn = function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'coordRegTable') return true;
            const row = table.row(dataIndex).node();
            return $(row).data('status') === filter;
        };
        fn._coordStatusFilter = true;
        $.fn.dataTable.ext.search.push(fn);
    }
    table.draw();
});

$('#btn-copy-public-link, .btn-copy-edit-link').on('click', function () {
    const url = $(this).data('url') || $('#coord-edit-link-input').val();
    if (!url) return;
    copyTextToClipboard(String(url)).then(function () {
        Swal.fire({ icon: 'success', title: 'Link copied', timer: 1200, showConfirmButton: false });
    });
});

$(document).on('click', '.modal-close-btn', function () {
    hideBsModal('#approve-modal');
    hideBsModal('#view-modal');
});

$(document).on('click', '.btn-view', function () {
    const id = $(this).data('id');
    $('#view-modal-body').html('Loading...');
    showBsModal('#view-modal');
    $.get('/coordinator-registrations/' + id, function (d) {
        const photoUrl = mediaFileUrl(d.photo);
        $('#view-modal-body').html(`
            <div class="d-flex align-items-start mb-3" style="gap:14px;">
                ${photoUrl ? `<img src="${photoUrl}" class="view-photo" alt="Photo">` : ''}
                <div>
                    <h5 class="mb-1">${esc(d.instructor_name)}</h5>
                    <div class="small text-muted mb-1">${esc(d.instructor_code)}</div>
                    <span class="status-pill ${esc(d.status)}">${esc(d.status)}</span>
                    <div class="small mt-1"><strong>District Coordinator</strong> application</div>
                </div>
            </div>
            <div class="view-section"><h6>Personal</h6><div class="view-grid">
                <div><span class="view-label">Father Name</span><span class="view-val">${esc(d.father_name)}</span></div>
                <div><span class="view-label">Blood Group</span><span class="view-val">${esc(d.blood_group)}</span></div>
                <div><span class="view-label">Email</span><span class="view-val">${esc(d.email)}</span></div>
                <div><span class="view-label">Phone</span><span class="view-val">${esc(d.instructor_number)}</span></div>
                <div><span class="view-label">Aadhar</span><span class="view-val">${esc(d.aadhar_number)}</span></div>
                <div style="grid-column:1/-1;"><span class="view-label">Address</span><span class="view-val">${esc(d.address)}</span></div>
            </div></div>
            <div class="view-section"><h6>Location</h6><div class="view-grid">
                <div><span class="view-label">State</span><span class="view-val">${esc(d.state && d.state.name)}</span></div>
                <div><span class="view-label">District</span><span class="view-val">${esc(d.district)}</span></div>
                <div><span class="view-label">Block</span><span class="view-val">${esc(d.block)}</span></div>
                <div><span class="view-label">Martial Art</span><span class="view-val">${esc(d.martial_art_type)}</span></div>
            </div></div>
            <div class="view-section">
                <h6>Documents</h6>
                <div>
                    ${docChip('Aadhar', d.aadhar_doc)}
                    ${docChip('Qualification', d.qualification_doc)}
                    ${docChip('Martial Art Cert', d.martial_art_doc)}
                    ${docChip('Photo', d.photo)}
                </div>
            </div>
            ${d.admin_remarks ? `<div class="view-section"><h6>Admin Remarks</h6><div class="view-val">${esc(d.admin_remarks)}</div></div>` : ''}
            ${editLinkSection(d.edit_url)}
            ${d.rejection_note ? `<div class="view-section"><h6>Rejection Note</h6><div class="view-val">${esc(d.rejection_note)}</div></div>` : ''}
        `);
    }).fail(function () {
        $('#view-modal-body').html('<div class="text-danger">Failed to load details.</div>');
    });
});

$(document).on('click', '.btn-approve', function () {
    $('#registration_id').val($(this).data('id'));
    $('#approve-name').text($(this).data('name'));
    $('#approve-email').text('(' + $(this).data('email') + ')');
    $('#approve_code_label').text($(this).data('code') || '—');
    $('#approve_password').val('SOPL@1634');
    showBsModal('#approve-modal');
});

$('#btn-approve-save').on('click', function () {
    const id = $('#registration_id').val();
    $.ajax({
        type: 'POST',
        url: '/coordinator-registrations/' + id + '/approve',
        data: { password: $('#approve_password').val() },
        dataType: 'json',
        success: function (res) {
            Swal.fire({ icon: 'success', title: res.message || 'Approved', timer: 2500, showConfirmButton: false })
                .then(function () { location.reload(); });
        },
        error: function (xhr) {
            let msg = 'Something went wrong';
            try { msg = JSON.parse(xhr.responseText).message || msg; } catch (e) {}
            Swal.fire({ icon: 'error', title: msg });
        }
    });
});

$(document).on('click', '.btn-revision', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Ask for correction',
        html: 'Applicant will get email with remarks and edit link.',
        input: 'textarea',
        inputPlaceholder: 'e.g. Wrong Aadhar / Photo not clear',
        showCancelButton: true,
        confirmButtonText: 'Send Remarks',
        confirmButtonColor: '#f0ad4e',
        inputValidator: (value) => { if (!value) return 'Remarks are required'; }
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.ajax({
            type: 'POST',
            url: '/coordinator-registrations/' + id + '/request-revision',
            data: { admin_remarks: result.value },
            dataType: 'json',
            success: function (res) {
                const editUrl = res.edit_url || '';
                const linkHtml = editUrl
                    ? '<div class="text-start mt-3"><div class="small text-muted mb-1">Share this edit link if needed:</div>'
                        + '<div class="edit-link-box"><input type="text" class="form-control form-control-sm" readonly value="' + editUrl + '" id="revision-edit-link">'
                        + '<button type="button" class="btn btn-sm btn-outline-primary" id="btn-copy-revision-link">Copy</button></div></div>'
                    : '';
                Swal.fire({
                    icon: 'success',
                    title: res.message || 'Remarks sent',
                    html: linkHtml,
                    confirmButtonText: 'OK',
                    didOpen: function () {
                        $('#btn-copy-revision-link').on('click', function () {
                            copyTextToClipboard($('#revision-edit-link').val());
                        });
                    }
                }).then(function () { location.reload(); });
            },
            error: function (xhr) {
                let msg = 'Something went wrong';
                try { msg = JSON.parse(xhr.responseText).message || msg; } catch (e) {}
                Swal.fire({ icon: 'error', title: msg });
            }
        });
    });
});

$(document).on('click', '.btn-reject', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Reject registration?',
        input: 'textarea',
        inputPlaceholder: 'Optional rejection note',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        confirmButtonColor: '#c0392b'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.ajax({
            type: 'POST',
            url: '/coordinator-registrations/' + id + '/reject',
            data: { rejection_note: result.value || '' },
            dataType: 'json',
            success: function (res) {
                Swal.fire({ icon: 'success', title: res.message || 'Rejected', timer: 2000, showConfirmButton: false })
                    .then(function () { location.reload(); });
            },
            error: function (xhr) {
                let msg = 'Something went wrong';
                try { msg = JSON.parse(xhr.responseText).message || msg; } catch (e) {}
                Swal.fire({ icon: 'error', title: msg });
            }
        });
    });
});
</script>
@endsection
