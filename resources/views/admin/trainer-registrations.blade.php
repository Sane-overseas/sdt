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
    .reg-admin-wrap { max-width: 100%; }
    .reg-stat {
        background: #fff;
        border: 1px solid #e3e8ef;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s;
        height: 100%;
    }
    .reg-stat:hover, .reg-stat.active {
        border-color: #1a73a7;
        box-shadow: 0 2px 8px rgba(26,115,167,.12);
    }
    .reg-stat .n { font-size: 1.5rem; font-weight: 700; line-height: 1.1; color: #1a3a52; }
    .reg-stat .l { font-size: .8rem; color: #6b7c8c; text-transform: uppercase; letter-spacing: .03em; }
    .reg-stat.pending .n { color: #b78105; }
    .reg-stat.revision .n { color: #0c6e9e; }
    .reg-stat.approved .n { color: #1e7e34; }
    .reg-stat.rejected .n { color: #c0392b; }
    .reg-link-box {
        background: #f4f8fb;
        border: 1px solid #d7e4ef;
        border-radius: 8px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .reg-link-box a { word-break: break-all; font-size: .9rem; }
    .reg-table-card {
        background: #fff;
        border: 1px solid #e3e8ef;
        border-radius: 8px;
        padding: 16px;
    }
    #registrationTable td, #registrationTable th { vertical-align: middle; font-size: .9rem; }
    .reg-name { font-weight: 600; color: #1a3a52; }
    .reg-code { font-size: .78rem; color: #6b7c8c; font-family: monospace; }
    .reg-actions { display: flex; flex-wrap: wrap; gap: 4px; justify-content: flex-start; min-width: 200px; }
    .reg-actions .btn { font-size: .75rem; padding: 3px 8px; white-space: nowrap; }
    .status-pill {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-pill.pending { background: #fff3cd; color: #856404; }
    .status-pill.revision { background: #d1ecf1; color: #0c5460; }
    .edit-link-box {
        display: flex;
        gap: 8px;
        align-items: stretch;
        margin-top: 8px;
    }
    .edit-link-box input {
        flex: 1;
        font-size: 12px;
        font-family: monospace;
    }
    .status-pill.approved { background: #d4edda; color: #155724; }
    .status-pill.rejected { background: #f8d7da; color: #721c24; }
    .view-section {
        border: 1px solid #e8eef3;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 12px;
        background: #fafcfd;
    }
    .view-section h6 {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7c8c;
        margin-bottom: 10px;
        font-weight: 700;
    }
    .view-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; }
    .view-grid .full { grid-column: 1 / -1; }
    .view-label { font-size: .72rem; color: #8a9aab; display: block; }
    .view-val { font-size: .92rem; color: #1a3a52; font-weight: 500; }
    .doc-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid #d7e4ef;
        border-radius: 6px;
        padding: 6px 10px;
        margin: 0 6px 6px 0;
        font-size: .85rem;
        text-decoration: none !important;
        color: #1a73a7 !important;
    }
    .doc-chip:hover { background: #eef6fb; }
    .view-photo {
        width: 96px;
        height: 96px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #d7e4ef;
        background: #eee;
    }
    .modal-header .modal-close-btn,
    .modal-header .close {
        position: relative;
        z-index: 1056;
        color: #fff !important;
        opacity: 1 !important;
        text-shadow: none;
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
        background: transparent;
        border: 0;
        padding: 0 4px;
        cursor: pointer;
    }
    .modal-header .modal-close-btn:hover,
    .modal-header .close:hover { opacity: .85 !important; color: #fff !important; }
    @media (max-width: 576px) {
        .view-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container mt-2 reg-admin-wrap">
    <div class="row margin-tb align-items-center">
        <div class="col-md-8">
            <h2 class="heading mb-1">Trainer Registrations</h2>
            <p class="text-muted mb-0 small">Review public applications — approve, ask correction, or reject.</p>
        </div>
        <div class="col-md-4 text-right">
            <a class="btn btn-secondary" href="{{ route('add_trainers') }}">Back to Trainers</a>
        </div>
    </div>

    @if(isset($currentState) && $currentState)
    <div class="alert alert-info py-2">
        Showing registrations for state: <strong>{{ $currentState->name }}</strong> ({{ $currentState->code }})
    </div>
    @endif

    <div class="reg-link-box">
        <strong class="mb-0">Public form:</strong>
        <a href="{{ route('trainer.register') }}" target="_blank" id="public-reg-link">{{ route('trainer.register') }}</a>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-copy-link">Copy link</button>
    </div>

    <div class="row mb-3">
        <div class="col-6 col-md">
            <div class="reg-stat active" data-filter="">
                <div class="n">{{ $counts['all'] }}</div>
                <div class="l">All</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="reg-stat pending" data-filter="pending">
                <div class="n">{{ $counts['pending'] }}</div>
                <div class="l">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="reg-stat revision" data-filter="revision">
                <div class="n">{{ $counts['revision'] }}</div>
                <div class="l">Correction</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="reg-stat approved" data-filter="approved">
                <div class="n">{{ $counts['approved'] }}</div>
                <div class="l">Approved</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="reg-stat rejected" data-filter="rejected">
                <div class="n">{{ $counts['rejected'] }}</div>
                <div class="l">Rejected</div>
            </div>
        </div>
    </div>

    <div class="reg-table-card">
        <table class="table table-bordered table-hover mb-0" id="registrationTable">
            <thead>
                <tr>
                    <th>Trainer</th>
                    <th>Contact</th>
                    <th>District</th>
                    <th>Martial Art</th>
                    <th>Applied</th>
                    <th>Status</th>
                    <th>StatusKey</th>
                    <th style="min-width:210px;">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($registrations as $row)
                <tr>
                    <td>
                        <div class="reg-name">{{ $row->instructor_name }}</div>
                        <div class="reg-code">{{ $row->instructor_code ?? '—' }}</div>
                        <div class="small text-muted">{{ $row->father_name ? 'S/o '.$row->father_name : '' }}</div>
                    </td>
                    <td>
                        <div>{{ $row->email }}</div>
                        <div class="small text-muted">{{ $row->instructor_number }}</div>
                    </td>
                    <td>{{ $row->district }}</td>
                    <td>{{ $row->martial_art_type ?? '—' }}</td>
                    <td data-order="{{ $row->created_at?->timestamp }}">{{ $row->created_at?->format('d-m-Y') }}<br><small class="text-muted">{{ $row->created_at?->format('H:i') }}</small></td>
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
                            @if($row->rejection_note)
                                <div class="small text-muted mt-1">{{ $row->rejection_note }}</div>
                            @endif
                        @endif
                    </td>
                    <td>{{ $row->status }}</td>
                    <td>
                        <div class="reg-actions">
                            <button type="button" class="btn btn-secondary btn-view" data-id="{{ $row->id }}">View</button>
                            @if(in_array($row->status, ['pending', 'revision'], true))
                                <a href="{{ route('trainer.registrations.edit', $row->id) }}" class="btn btn-primary">Edit</a>
                            @endif
                            @if($row->status === 'pending')
                                <button type="button" class="btn btn-success btn-approve"
                                    data-id="{{ $row->id }}"
                                    data-name="{{ $row->instructor_name }}"
                                    data-email="{{ $row->email }}"
                                    data-code="{{ $row->instructor_code }}"
                                    data-reference="{{ $row->reference_by }}"
                                    data-cordinator-id="{{ $row->reference_cordinator_id }}">
                                    Approve
                                </button>
                                <button type="button" class="btn btn-warning btn-revision" data-id="{{ $row->id }}">
                                    Correction
                                </button>
                                <button type="button" class="btn btn-danger btn-reject" data-id="{{ $row->id }}">
                                    Reject
                                </button>
                            @elseif($row->status === 'approved' && $row->user_id)
                                <a href="{{ url('getData/'.$row->user_id) }}" class="btn btn-info">Open Trainer</a>
                            @elseif($row->status === 'revision')
                                <button type="button" class="btn btn-danger btn-reject" data-id="{{ $row->id }}">Reject</button>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Approve Trainer Registration</h4>
                <button type="button" class="close modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    Approving: <strong id="approve-name"></strong>
                    <span class="text-muted" id="approve-email"></span>
                </p>
                <form id="approveForm">
                    <input type="hidden" id="registration_id" name="registration_id">
                    <div class="form-row">
                        <div class="form-group col">
                            <label>Trainer ID / Code</label>
                            <input type="text" class="form-control" name="code" id="approve_code" readonly>
                            <small class="text-muted">Auto-generated on registration</small>
                        </div>
                        <div class="form-group col">
                            <label>Password</label>
                            <input type="text" class="form-control" name="password" id="approve_password" value="SOPL@1634">
                        </div>
                        <div class="form-group col">
                            <label>Cordinator <span class="text-danger">*</span></label>
                            <select name="cordinator" class="form-control" id="approve_cordinator" required>
                                <option value="">Select Cordinator</option>
                                @foreach($cordinator as $c)
                                    <option value="{{ $c->id }}" data-name="{{ $c->cordinator_name }}">{{ $c->cordinator_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col">
                            <label>Amount per School</label>
                            <input type="number" class="form-control" name="amount" id="approve_amount">
                        </div>
                        <div class="form-group col">
                            <label>Incentive Amount</label>
                            <input type="number" class="form-control" name="extra_amount" id="approve_extra_amount">
                        </div>
                    </div>
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
            <div class="modal-body" id="view-modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
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
        return;
    }
    el.classList.remove('show');
    el.style.display = 'none';
    el.setAttribute('aria-hidden', 'true');
    document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('overflow');
}

function showBsModal(selector) {
    const el = document.querySelector(selector);
    if (!el) return;
    try {
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(el).show();
            return;
        }
    } catch (e) {}
    if (window.jQuery && typeof jQuery(el).modal === 'function') {
        jQuery(el).modal('show');
    }
}

$(document).on('click', '#view-modal .modal-close-btn, #view-modal [data-dismiss="modal"], #view-modal [data-bs-dismiss="modal"]', function (e) {
    e.preventDefault();
    e.stopPropagation();
    hideBsModal('#view-modal');
});
$(document).on('click', '#approve-modal .modal-close-btn, #approve-modal [data-dismiss="modal"], #approve-modal [data-bs-dismiss="modal"]', function (e) {
    e.preventDefault();
    e.stopPropagation();
    hideBsModal('#approve-modal');
});

const table = $('#registrationTable').DataTable({
    pageLength: 25,
    order: [],
    columnDefs: [
        { targets: 6, visible: false, searchable: true }
    ]
});

$('.reg-stat').on('click', function () {
    $('.reg-stat').removeClass('active');
    $(this).addClass('active');
    const filter = $(this).data('filter');
    table.column(6).search(filter ? '^' + filter + '$' : '', true, false).draw();
});

$('#btn-copy-link').on('click', function () {
    const text = $('#public-reg-link').attr('href') || $('#public-reg-link').text();
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
            Swal.fire({ icon: 'success', title: 'Link copied', timer: 1200, showConfirmButton: false });
        });
    } else {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        Swal.fire({ icon: 'success', title: 'Link copied', timer: 1200, showConfirmButton: false });
    }
});

$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

function mediaFileUrl(path) {
    if (!path) return null;
    const file = String(path).split('/').pop();
    return '/m/r/' + encodeURIComponent(file);
}

function docChip(label, path) {
    const url = mediaFileUrl(path);
    if (!url) return `<span class="text-muted small">${label}: —</span>`;
    return `<a class="doc-chip" href="${url}" target="_blank" rel="noopener">${label}</a>`;
}

function esc(v) {
    if (v === null || v === undefined || v === '') return '—';
    return String(v)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function copyTextToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try {
        document.execCommand('copy');
        return Promise.resolve();
    } finally {
        document.body.removeChild(ta);
    }
}

function editLinkSection(editUrl) {
    if (!editUrl) return '';
    const safeUrl = esc(editUrl);
    return `
        <div class="view-section">
            <h6>Trainer Edit Link</h6>
            <div class="edit-link-box">
                <input type="text" class="form-control form-control-sm" readonly value="${safeUrl}" id="trainer-edit-link-input">
                <button type="button" class="btn btn-sm btn-outline-primary btn-copy-edit-link" data-url="${safeUrl}">Copy</button>
                <a href="${safeUrl}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Open</a>
            </div>  
        </div>
    `;
}

$(document).on('click', '.btn-copy-edit-link', function () {
    const url = $(this).data('url') || $('#trainer-edit-link-input').val();
    if (!url) return;
    copyTextToClipboard(String(url)).then(function () {
        Swal.fire({
            icon: 'success',
            title: 'Link copied',
            timer: 1500,
            showConfirmButton: false
        });
    }).catch(function () {
        Swal.fire({ icon: 'error', title: 'Could not copy link' });
    });
});

$(document).on('click', '.btn-view', function () {
    const id = $(this).data('id');
    $('#view-modal-body').html('Loading...');
    showBsModal('#view-modal');

    $.get('/trainer-registrations/' + id, function (d) {
        const photoUrl = mediaFileUrl(d.photo);
        const statusClass = d.status || '';
        const html = `
            <div class="d-flex align-items-start mb-3" style="gap:14px;">
                ${photoUrl
                    ? `<img src="${photoUrl}" class="view-photo" alt="Photo" onerror="this.style.display='none'">`
                    : `<div class="view-photo d-flex align-items-center justify-content-center text-muted small">No photo</div>`}
                <div>
                    <h5 class="mb-1" style="color:#1a3a52;">${esc(d.instructor_name)}</h5>
                    <div class="reg-code mb-1">${esc(d.instructor_code)}</div>
                    <span class="status-pill ${esc(statusClass)}">${esc(d.status)}</span>
                </div>
            </div>

            <div class="view-section">
                <h6>Personal</h6>
                <div class="view-grid">
                    <div><span class="view-label">Father Name</span><span class="view-val">${esc(d.father_name)}</span></div>
                    <div><span class="view-label">Blood Group</span><span class="view-val">${esc(d.blood_group)}</span></div>
                    <div><span class="view-label">Email</span><span class="view-val">${esc(d.email)}</span></div>
                    <div><span class="view-label">Phone</span><span class="view-val">${esc(d.instructor_number)}</span></div>
                    <div><span class="view-label">Aadhar Number</span><span class="view-val">${esc(d.aadhar_number)}</span></div>
                    <div class="full"><span class="view-label">Address</span><span class="view-val">${esc(d.address)}</span></div>
                </div>
            </div>

            <div class="view-section">
                <h6>Location &amp; Training</h6>
                <div class="view-grid">
                    <div><span class="view-label">State</span><span class="view-val">${esc(d.state && d.state.name)}</span></div>
                    <div><span class="view-label">District</span><span class="view-val">${esc(d.district)}</span></div>
                    <div><span class="view-label">Block</span><span class="view-val">${esc(d.block)}</span></div>
                    <div><span class="view-label">Martial Art</span><span class="view-val">${esc(d.martial_art_type)}</span></div>
                    <div><span class="view-label">Reference By</span><span class="view-val">${esc(d.reference_by)}</span></div>
                    <div><span class="view-label">Comment</span><span class="view-val">${esc(d.comment)}</span></div>
                </div>
            </div>

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
        `;
        $('#view-modal-body').html(html);
    }).fail(function () {
        $('#view-modal-body').html('<div class="text-danger">Failed to load details.</div>');
    });
});

$(document).on('click', '.btn-approve', function () {
    $('#registration_id').val($(this).data('id'));
    $('#approve-name').text($(this).data('name'));
    $('#approve-email').text('(' + $(this).data('email') + ')');
    $('#approveForm')[0].reset();
    $('#approve_code').val($(this).data('code') || '');
    $('#approve_password').val('SOPL@1634');

    const cordinatorId = $(this).data('cordinator-id');
    const reference = String($(this).data('reference') || '').trim().toLowerCase();
    let matched = false;

    if (cordinatorId) {
        $('#approve_cordinator').val(String(cordinatorId));
        matched = !!$('#approve_cordinator').val();
    }

    if (!matched && reference) {
        $('#approve_cordinator option').each(function () {
            const name = String($(this).data('name') || $(this).text() || '').trim().toLowerCase();
            if (name && name === reference) {
                $('#approve_cordinator').val($(this).val());
                matched = true;
                return false;
            }
        });
    }
    if (!matched) {
        $('#approve_cordinator').val('');
    }

    showBsModal('#approve-modal');
});

$('#btn-approve-save').on('click', function () {
    const id = $('#registration_id').val();
    const formData = $('#approveForm').serialize();

    $.ajax({
        type: 'POST',
        url: '/trainer-registrations/' + id + '/approve',
        data: formData,
        dataType: 'json',
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: res.message || 'Approved',
                timer: 2500,
                showConfirmButton: false
            }).then(function () { location.reload(); });
        },
        error: function (xhr) {
            let msg = 'Something went wrong';
            try {
                const obj = JSON.parse(xhr.responseText);
                msg = obj.message || Object.values(obj.errors || {}).flat().join(', ') || msg;
            } catch (e) {}
            Swal.fire({ icon: 'error', title: msg });
        }
    });
});

$(document).on('click', '.btn-revision', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Ask for correction',
        html: 'Trainer will get email with remarks and edit link.<br>Form stays locked until then.',
        input: 'textarea',
        inputPlaceholder: 'e.g. Wrong Aadhar uploaded / Incorrect address / Photo not clear',
        inputAttributes: { 'aria-label': 'Admin remarks' },
        showCancelButton: true,
        confirmButtonText: 'Send Remarks Email',
        confirmButtonColor: '#f0ad4e',
        inputValidator: (value) => {
            if (!value) return 'Remarks are required';
        }
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.ajax({
            type: 'POST',
            url: '/trainer-registrations/' + id + '/request-revision',
            data: { admin_remarks: result.value },
            dataType: 'json',
            success: function (res) {
                const editUrl = res.edit_url || '';
                const linkHtml = editUrl
                    ? '<div class="text-start mt-3"><div class="small text-muted mb-1">Share this edit link manually if needed:</div>'
                        + '<div class="edit-link-box"><input type="text" class="form-control form-control-sm" readonly value="' + editUrl + '" id="revision-edit-link">'
                        + '<button type="button" class="btn btn-sm btn-outline-primary" id="btn-copy-revision-link">Copy</button></div></div>'
                    : '';

                Swal.fire({
                    icon: res.edit_url && res.message && res.message.indexOf('could not be sent') !== -1 ? 'warning' : 'success',
                    title: res.message || 'Correction remarks sent',
                    html: linkHtml,
                    confirmButtonText: 'OK',
                    didOpen: function () {
                        $('#btn-copy-revision-link').on('click', function () {
                            copyTextToClipboard($('#revision-edit-link').val()).then(function () {
                                Swal.showValidationMessage('Link copied');
                                setTimeout(function () { Swal.resetValidationMessage(); }, 1500);
                            });
                        });
                    }
                }).then(function () { location.reload(); });
            },
            error: function (xhr) {
                let msg = 'Something went wrong';
                try {
                    const obj = JSON.parse(xhr.responseText);
                    msg = obj.message || msg;
                } catch (e) {}
                Swal.fire({ icon: 'error', title: msg });
            }
        });
    });
});

$(document).on('click', '.btn-reject', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Reject this registration?',
        input: 'text',
        inputPlaceholder: 'Rejection note (optional)',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        confirmButtonColor: '#d33'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.ajax({
            type: 'POST',
            url: '/trainer-registrations/' + id + '/reject',
            data: { rejection_note: result.value || '' },
            dataType: 'json',
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: res.message || 'Rejected',
                    timer: 2000,
                    showConfirmButton: false
                }).then(function () { location.reload(); });
            },
            error: function (xhr) {
                let msg = 'Something went wrong';
                try {
                    const obj = JSON.parse(xhr.responseText);
                    msg = obj.message || msg;
                } catch (e) {}
                Swal.fire({ icon: 'error', title: msg });
            }
        });
    });
});
</script>
@endsection
