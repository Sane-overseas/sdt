@extends('layouts.app')

@section('content')
@php
    $r = $registration;
    $isImagePath = function (?string $path): bool {
        if (!$path) return false;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    };
    $docUrl = function (?string $path) {
        if (!$path) return null;
        return url('/m/r/'.rawurlencode(basename($path))).'?v='.substr(md5($path), 0, 8);
    };
    $docs = [
        'aadhar_doc' => ['label' => 'Aadhar Document', 'accept' => '.jpg,.jpeg,.png,.pdf', 'path' => $r->aadhar_doc],
        'qualification_doc' => ['label' => 'Qualification', 'accept' => '.jpg,.jpeg,.png,.pdf', 'path' => $r->qualification_doc],
        'martial_art_doc' => ['label' => 'Martial Art Certificate', 'accept' => '.jpg,.jpeg,.png,.pdf', 'path' => $r->martial_art_doc],
        'photo' => ['label' => 'Photo', 'accept' => '.jpg,.jpeg,.png', 'path' => $r->photo, 'ratio' => '3/4'],
    ];
@endphp
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    .reg-edit-wrap { max-width: 980px; }
    .reg-edit-card {
        background: #fff;
        border: 1px solid #e3e8ef;
        border-radius: 8px;
        padding: 18px 20px;
        margin-bottom: 16px;
    }
    .reg-edit-card h5 {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7c8c;
        font-weight: 700;
        margin-bottom: 14px;
    }
    .doc-row {
        border: 1px solid #e8eef3;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 12px;
        background: #fafcfd;
    }
    .doc-row-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }
    .doc-row-top label { margin: 0; font-weight: 600; color: #1a3a52; }
    .doc-actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .doc-status { font-size: .8rem; color: #1e7e34; display: none; margin-top: 6px; }
    .doc-status.show { display: block; }
    #doc-crop-modal .modal-dialog {
        max-width: 560px;
        margin: 1.2rem auto;
    }
    #doc-crop-modal .modal-body {
        padding: 12px 16px 8px;
    }
    .crop-modal-box {
        width: 100%;
        height: 420px;
        background: #1a1a1a;
        overflow: hidden;
        position: relative;
        border-radius: 6px;
    }
    .crop-modal-box img {
        display: block;
        max-width: 100%;
    }
    /* Cropper fills the fixed box */
    .crop-modal-box .cropper-container {
        width: 100% !important;
        height: 100% !important;
    }
    .modal-header .modal-title,
    .modal-header h5 { color: #fff; margin: 0; }
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
    .crop-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 12px; }
</style>

<div class="container mt-2 reg-edit-wrap">
    <div class="row margin-tb align-items-center mb-2">
        <div class="col-md-8">
            <h2 class="heading mb-1">Edit Registration</h2>
            <p class="text-muted mb-0 small">
                {{ $r->instructor_name }}
                <span class="font-monospace">· {{ $r->instructor_code }}</span>
                <span class="ml-2" style="display:inline-block;padding:2px 8px;border-radius:999px;font-size:.75rem;font-weight:600;background:#fff3cd;color:#856404;">
                    {{ ucfirst($r->status) }}
                </span>
            </p>
        </div>
        <div class="col-md-4 text-md-right">
            <a href="{{ route('trainer.registrations') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('trainer.registrations.update', $r->id) }}" enctype="multipart/form-data" id="adminRegEditForm">
        @csrf
        @method('PUT')

        @foreach($docs as $field => $meta)
            <input type="hidden" name="{{ $field }}_cropped" id="{{ $field }}_cropped" value="">
        @endforeach

        <div class="reg-edit-card">
            <h5>Personal Details</h5>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="instructor_name" class="form-control" value="{{ old('instructor_name', $r->instructor_name) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Father Name <span class="text-danger">*</span></label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $r->father_name) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $r->email) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="instructor_number" id="instructor_number" class="form-control"
                           value="{{ old('instructor_number', $r->instructor_number) }}"
                           inputmode="numeric" maxlength="10" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Aadhar Number <span class="text-danger">*</span></label>
                    <input type="text" name="aadhar_number" id="aadhar_number" class="form-control"
                           value="{{ old('aadhar_number', $r->aadhar_number) }}"
                           inputmode="numeric" maxlength="12" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Blood Group <span class="text-danger">*</span></label>
                    <select name="blood_group" class="form-control" required>
                        <option value="">Select</option>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group', $r->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12">
                    <label>Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="2" required>{{ old('address', $r->address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="reg-edit-card">
            <h5>Location &amp; Training</h5>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>State <span class="text-danger">*</span></label>
                    <select name="state_id" id="state_id" class="form-control" required>
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ (int) old('state_id', $r->state_id) === (int) $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>District <span class="text-danger">*</span></label>
                    <select name="district_id" id="district_id" class="form-control" required>
                        <option value="">Select District</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ (int) old('district_id', $selectedDistrictId) === (int) $d->id ? 'selected' : '' }}>
                                {{ $d->district }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Block <span class="text-danger">*</span></label>
                    <select name="block" id="block" class="form-control" required>
                        <option value="">Select Block</option>
                        @foreach($blocks as $b)
                            <option value="{{ $b->block }}" {{ old('block', $r->block) === $b->block ? 'selected' : '' }}>
                                {{ $b->block }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Martial Art Type <span class="text-danger">*</span></label>
                    <input type="text" name="martial_art_type" class="form-control" value="{{ old('martial_art_type', $r->martial_art_type) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Reference / Coordinator <span class="text-danger">*</span></label>
                    <select name="reference_by" id="reference_by" class="form-control" required>
                        <option value="">Select Coordinator</option>
                        @foreach($coordinators as $c)
                            <option value="{{ $c->id }}"
                                {{ (int) old('reference_by', $r->reference_cordinator_id) === (int) $c->id ? 'selected' : '' }}>
                                {{ $c->cordinator_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12">
                    <label>Comment</label>
                    <textarea name="comment" class="form-control" rows="2">{{ old('comment', $r->comment) }}</textarea>
                </div>
            </div>
        </div>

        <div class="reg-edit-card">
            <h5>Documents</h5>
            <p class="text-muted small mb-3">File replace is optional. Click <strong>View / Crop</strong> on an image to crop or rotate that document.</p>

            @foreach($docs as $field => $meta)
                @php
                    $url = $docUrl($meta['path'] ?? null);
                    $canCrop = $isImagePath($meta['path'] ?? null);
                @endphp
                <div class="doc-row" data-field="{{ $field }}">
                    <div class="doc-row-top">
                        <label>{{ $meta['label'] }}</label>
                        <div class="doc-actions">
                            @if($url && !$canCrop)
                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open PDF</a>
                            @endif
                            <button type="button"
                                    class="btn btn-sm btn-info btn-view-doc"
                                    data-field="{{ $field }}"
                                    data-label="{{ $meta['label'] }}"
                                    data-url="{{ $canCrop ? $url : '' }}"
                                    data-ratio="{{ $meta['ratio'] ?? '' }}"
                                    @if(!$canCrop) disabled title="Upload/select an image to crop" @endif>
                                View / Crop
                            </button>
                        </div>
                    </div>
                    <input type="file"
                           name="{{ $field }}"
                           id="file_{{ $field }}"
                           class="form-control doc-file-input"
                           data-field="{{ $field }}"
                           data-label="{{ $meta['label'] }}"
                           data-ratio="{{ $meta['ratio'] ?? '' }}"
                           accept="{{ $meta['accept'] }}">
                    <div class="doc-status" id="status_{{ $field }}">Cropped image saved.</div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-end mb-4" style="gap:8px;">
            <a href="{{ route('trainer.registrations') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

{{-- Single crop modal — opens only for the document you View --}}
<div class="modal fade" id="doc-crop-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop: <span id="crop-doc-label">Document</span></h5>
                <button type="button" class="close modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="crop-modal-box" id="crop-modal-box">
                    <img id="crop-modal-img" src="" alt="Document">
                </div>
                <div class="crop-actions">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-rotate-left">Rotate Left</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-rotate-right">Rotate Right</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-reset-crop">Reset</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-apply-crop">Apply Crop</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
(function () {
    let cropper = null;
    let activeField = null;
    let pendingDataUrl = {};
    let pendingCrop = null; // { src, ratio, field }

    const img = document.getElementById('crop-modal-img');

    function hideModal(selector) {
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

    function showModal(selector) {
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

    function destroyCropper() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    function isImageFile(file) {
        if (!file) return false;
        if (file.type && file.type.indexOf('image/') === 0) return true;
        return /\.(jpe?g|png|webp|gif)$/i.test(file.name || '');
    }

    function initCropperWhenReady() {
        if (!pendingCrop || !pendingCrop.src) return;
        destroyCropper();

        const start = function () {
            const opts = {
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: true,
                background: false,
                modal: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            };

            if (pendingCrop.ratio === '3/4' || pendingCrop.field === 'photo') {
                opts.aspectRatio = 3 / 4;
            }

            cropper = new Cropper(img, opts);
            setTimeout(function () {
                if (cropper) cropper.resize();
            }, 80);
        };

        img.onload = start;
        if (img.src === pendingCrop.src && img.complete) {
            img.onload = null;
            start();
        } else {
            img.src = pendingCrop.src;
        }
    }

    function openCropModal(field, label, src, ratio) {
        if (!src) {
            Swal.fire({ icon: 'info', title: 'No image', text: 'Select or upload an image first, then click View / Crop.' });
            return;
        }
        activeField = field;
        pendingCrop = { src: src, ratio: ratio || '', field: field };
        $('#crop-doc-label').text(label || field);
        destroyCropper();
        img.removeAttribute('src');
        showModal('#doc-crop-modal');
    }

    $('#doc-crop-modal').on('shown.bs.modal', function () {
        initCropperWhenReady();
    });

    $('#doc-crop-modal').on('hidden.bs.modal', function () {
        destroyCropper();
        activeField = null;
        pendingCrop = null;
        img.onload = null;
        img.removeAttribute('src');
    });

    // Explicit close — works with Bootstrap 4/5 and broken data-dismiss
    $(document).on('click', '#doc-crop-modal .modal-close-btn, #doc-crop-modal [data-dismiss="modal"], #doc-crop-modal [data-bs-dismiss="modal"]', function (e) {
        e.preventDefault();
        e.stopPropagation();
        hideModal('#doc-crop-modal');
    });

    $(document).on('click', '.btn-view-doc', function () {
        const field = $(this).data('field');
        const label = $(this).data('label');
        const ratio = $(this).data('ratio') || '';
        const fileInput = document.getElementById('file_' + field);
        const file = fileInput && fileInput.files && fileInput.files[0];

        if (pendingDataUrl[field]) {
            openCropModal(field, label, pendingDataUrl[field], ratio);
            return;
        }

        if (file && isImageFile(file)) {
            const reader = new FileReader();
            reader.onload = function (ev) {
                pendingDataUrl[field] = ev.target.result;
                openCropModal(field, label, ev.target.result, ratio);
            };
            reader.readAsDataURL(file);
            return;
        }

        const url = $(this).data('url');
        if (url) {
            openCropModal(field, label, url, ratio);
            return;
        }

        Swal.fire({ icon: 'info', title: 'No image', text: 'No image found for this document. Choose a new image file.' });
    });

    $('.doc-file-input').on('change', function () {
        const field = $(this).data('field');
        const file = this.files && this.files[0];
        const $btn = $('.btn-view-doc[data-field="' + field + '"]');
        delete pendingDataUrl[field];
        $('#' + field + '_cropped').val('');
        $('#status_' + field).removeClass('show');

        if (file && isImageFile(file)) {
            const reader = new FileReader();
            reader.onload = function (ev) {
                pendingDataUrl[field] = ev.target.result;
                $btn.prop('disabled', false).attr('data-url', '');
            };
            reader.readAsDataURL(file);
        } else if (file) {
            $btn.prop('disabled', true);
        }
    });

    $('#btn-rotate-left').on('click', function () { if (cropper) cropper.rotate(-90); });
    $('#btn-rotate-right').on('click', function () { if (cropper) cropper.rotate(90); });
    $('#btn-reset-crop').on('click', function () { if (cropper) cropper.reset(); });

    $('#btn-apply-crop').on('click', function () {
        if (!cropper || !activeField) return;
        const field = activeField;
        const isPhoto = field === 'photo';
        const canvas = cropper.getCroppedCanvas(
            isPhoto
                ? { width: 600, height: 800, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' }
                : { maxWidth: 1600, maxHeight: 2000, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' }
        );
        if (!canvas) return;

        const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
        const $btn = $('#btn-apply-crop');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            type: 'POST',
            url: @json(route('trainer.registrations.crop-document', $r->id)),
            data: {
                _token: $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                field: field,
                image: dataUrl,
            },
            dataType: 'json',
            success: function (res) {
                pendingDataUrl[field] = dataUrl;
                $('#' + field + '_cropped').val('');
                $('#file_' + field).val('');
                $('#status_' + field).addClass('show');
                $('.btn-view-doc[data-field="' + field + '"]')
                    .prop('disabled', false)
                    .attr('data-url', res.url || '');
                hideModal('#doc-crop-modal');
                Swal.fire({
                    icon: 'success',
                    title: res.message || 'Cropped image saved',
                    timer: 1400,
                    showConfirmButton: false,
                });
            },
            error: function (xhr) {
                let msg = 'Could not save cropped image';
                try {
                    const obj = JSON.parse(xhr.responseText);
                    msg = obj.message || msg;
                } catch (e) {}
                Swal.fire({ icon: 'error', title: msg });
            },
            complete: function () {
                $btn.prop('disabled', false).text('Apply Crop');
            }
        });
    });

    $('#instructor_number, #aadhar_number').on('input', function () {
        const max = this.id === 'aadhar_number' ? 12 : 10;
        this.value = this.value.replace(/\D/g, '').slice(0, max);
    });

    function loadDistricts(stateId) {
        $('#district_id').html('<option value="">Select District</option>');
        $('#block').html('<option value="">Select Block</option>');
        if (!stateId) return;
        $.get('/trainer-register/districts/' + stateId, function (list) {
            (list || []).forEach(function (d) {
                $('#district_id').append('<option value="' + d.id + '">' + d.district + '</option>');
            });
        });
    }

    function loadBlocks(districtId) {
        $('#block').html('<option value="">Select Block</option>');
        if (!districtId) return;
        $.get('/trainer-register/blocks/' + districtId, function (list) {
            (list || []).forEach(function (b) {
                $('#block').append('<option value="' + b.block + '">' + b.block + '</option>');
            });
        });
    }

    function loadCoordinators(districtId) {
        $('#reference_by').html('<option value="">Select Coordinator</option>');
        if (!districtId) return;
        $.get('/trainer-register/coordinators/' + districtId, function (list) {
            (list || []).forEach(function (c) {
                const cid = c.cordinator_id || c.id;
                $('#reference_by').append('<option value="' + cid + '">' + c.instructor_name + '</option>');
            });
        });
    }

    $('#state_id').on('change', function () {
        loadDistricts(this.value);
        $('#reference_by').html('<option value="">Select Coordinator</option>');
    });
    $('#district_id').on('change', function () {
        loadBlocks(this.value);
        loadCoordinators(this.value);
    });
})();
</script>
@endsection
