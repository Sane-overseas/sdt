<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($isEdit) ? 'Edit' : '' }} Trainer Registration - SOPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('/images/logo.jpg') }}"/>
    <style>
        :root {
            --ink: #142033;
            --navy: #0f2744;
            --teal: #0f766e;
            --teal-dark: #0b5f59;
            --amber: #c2410c;
            --page: #e8eef5;
            --card: #ffffff;
            --muted: #5b6b7c;
            --line: #d5dee8;
            --field-bg: #fbfcfe;
            --section-personal: #f0f7ff;
            --section-other: #f3faf7;
            --section-docs: #fff8f1;
            --section-terms: #f7f5ff;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.18), transparent 28%),
                radial-gradient(circle at left 20%, rgba(15, 39, 68, 0.16), transparent 32%),
                linear-gradient(180deg, #dbe5f0 0%, var(--page) 45%, #d7e2ee 100%);
        }

        .reg-card {
            max-width: 920px;
            margin: 36px auto 48px;
            background: var(--card);
            border-radius: 18px;
            border: 1px solid rgba(20, 32, 51, 0.08);
            box-shadow: 0 18px 50px rgba(15, 39, 68, 0.16);
            overflow: hidden;
        }

        .reg-header {
            background:
                linear-gradient(135deg, #0b1c33 0%, #143457 55%, #0f766e 140%);
            color: #fff;
            padding: 28px 30px 24px;
            text-align: center;
            position: relative;
        }

        .reg-header::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #0f766e, #ea580c, #0f766e);
        }

        .reg-header img {
            max-height: 58px;
            margin-bottom: 12px;
            background: #ffffff;
            padding: 10px 16px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,.18);
        }

        .reg-header h4 {
            margin: 0;
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .reg-header p {
            color: rgba(255,255,255,.78) !important;
        }

        .reg-body {
            padding: 28px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .form-section {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px 18px 20px;
            margin-bottom: 18px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
        }

        .form-section.personal { background: var(--section-personal); border-left: 4px solid #2563eb; }
        .form-section.other { background: var(--section-other); border-left: 4px solid var(--teal); }
        .form-section.docs { background: var(--section-docs); border-left: 4px solid #ea580c; }
        .form-section.terms {
            background: var(--section-terms);
            border-left: 4px solid #7c3aed;
            margin-bottom: 8px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin: 0 0 14px;
            padding: 0;
            border: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section.personal .section-title { color: #1d4ed8; }
        .form-section.other .section-title { color: var(--teal-dark); }
        .form-section.docs .section-title { color: #c2410c; }

        .section-title::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 0 4px rgba(0,0,0,.04);
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.92rem;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            background: var(--field-bg);
            border: 1.5px solid #c9d5e3;
            border-radius: 10px;
            min-height: 44px;
            color: var(--ink);
            box-shadow: none;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: #fff;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.18);
        }

        textarea.form-control {
            min-height: 78px;
        }

        .req { color: #dc2626; font-weight: 700; }

        .btn-submit {
            background: linear-gradient(180deg, #10968c 0%, var(--teal) 100%);
            border: none;
            padding: 11px 28px;
            font-weight: 700;
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 8px 18px rgba(15, 118, 110, 0.28);
        }

        .btn-submit:hover {
            background: linear-gradient(180deg, #0f837a 0%, var(--teal-dark) 100%);
            color: #fff;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px dashed #cbd5e1;
        }

        .form-actions a {
            color: var(--navy);
            font-weight: 600;
        }

        .current-file { font-size: 12px; }
        .current-file a { color: var(--teal-dark); font-weight: 600; }

        .field-error {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc2626;
        }

        .is-invalid {
            border-color: #dc2626 !important;
            background: #fff7f7 !important;
        }

        .tc-link {
            color: #2563eb;
            text-decoration: underline;
            cursor: pointer;
            font-weight: 700;
        }

        .tc-link:hover { color: #1d4ed8; }

        .tc-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .62);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .tc-modal-backdrop.show { display: flex; }

        .tc-modal {
            background: #fff;
            width: min(720px, 100%);
            max-height: 90vh;
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
        }

        .tc-modal-header {
            padding: 14px 18px;
            background: linear-gradient(135deg, var(--navy), #1e3a5f);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tc-modal-body {
            padding: 18px;
            overflow-y: auto;
            flex: 1;
            line-height: 1.6;
            color: #1f2937;
            background: #fcfcfd;
        }

        .tc-modal-footer {
            padding: 12px 18px;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            background: #fff;
        }

        .tc-hint { font-size: 13px; color: var(--muted); }

        #tc_agree_btn {
            background: var(--teal);
            border-color: var(--teal);
        }

        #tc_agree_btn:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        @media (max-width: 576px) {
            .reg-card { margin: 16px 10px 28px; border-radius: 14px; }
            .reg-body { padding: 16px; }
            .form-actions { flex-direction: column; gap: 12px; align-items: stretch; }
            .btn-submit { width: 100%; }
        }
    </style>
</head>
<body>
@php
    $r = $registration ?? null;
    $draftDocs = $draftDocs ?? [];
    $v = function ($field, $default = '') use ($r) {
        return old($field, $r->{$field} ?? $default);
    };
    $err = function ($field) use ($errors) {
        return $errors->first($field);
    };
    $hasDoc = function ($field) use ($r, $draftDocs) {
        return !empty($r->{$field}) || !empty($draftDocs[$field]['path']);
    };
@endphp
<div class="container">
    <div class="reg-card">
        <div class="reg-header">
            <img src="{{ asset('/images/logo1.png') }}" alt="SOPL">
            <h4 class="mb-1">{{ !empty($isEdit) ? 'Correct / Update Registration' : 'Trainer Registration' }}</h4>
            @if(!empty($isEdit))
                <p class="mb-0 small opacity-75">Update the wrong details or documents, then resubmit.</p>
            @endif
        </div>
        <div class="reg-body">
            @if(!empty($isEdit) && $r && $r->admin_remarks)
                <div class="alert alert-warning">
                    <strong>Admin Remarks:</strong><br>{{ $r->admin_remarks }}
                </div>
            @endif

            @if(!empty($draftDocs))
                <div class="alert alert-info">
                    <!-- Previously uploaded documents are saved. Fix the error below and submit again — you only need to re-upload a document if it is missing or was rejected. -->
                </div>
            @endif

            <form method="POST" id="registrationForm"
                  action="{{ !empty($isEdit) ? route('trainer.register.update', $r->edit_token) : route('trainer.register.store') }}"
                  enctype="multipart/form-data" novalidate>
                @csrf
                @if(!empty($isEdit))
                    @method('PUT')
                @endif

                <div class="form-section personal">
                <div class="section-title">Personal Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="req">*</span></label>
                        <input type="text" name="instructor_name" class="form-control {{ $err('instructor_name') ? 'is-invalid' : '' }}" value="{{ $v('instructor_name') }}" required>
                        @if($err('instructor_name'))<div class="field-error">{{ $err('instructor_name') }}</div>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Father Name <span class="req">*</span></label>
                        <input type="text" name="father_name" class="form-control {{ $err('father_name') ? 'is-invalid' : '' }}" value="{{ $v('father_name') }}" required>
                        @if($err('father_name'))<div class="field-error">{{ $err('father_name') }}</div>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control {{ $err('email') ? 'is-invalid' : '' }}" value="{{ $v('email') }}" required>
                        <div class="form-text" style="color:#0f766e;font-size:12.5px;margin-top:6px;">
                            Please enter a correct email. Login credentials will be sent to you through this mail.
                        </div>
                        @if($err('email'))<div class="field-error">{{ $err('email') }}</div>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="req">*</span></label>
                        <input type="text" name="instructor_number" id="instructor_number" class="form-control {{ $err('instructor_number') ? 'is-invalid' : '' }}"
                               value="{{ $v('instructor_number') }}"
                               inputmode="numeric" maxlength="10" pattern="[0-9]{10}"
                               placeholder="10 digit mobile number" required>
                        <div class="field-error d-none" id="phone-error">Phone number must be exactly 10 digits.</div>
                        @if($err('instructor_number'))<div class="field-error">{{ $err('instructor_number') }}</div>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aadhar Number <span class="req">*</span></label>
                        <input type="text" name="aadhar_number" id="aadhar_number" class="form-control {{ $err('aadhar_number') ? 'is-invalid' : '' }}"
                               value="{{ $v('aadhar_number') }}"
                               inputmode="numeric" maxlength="12" pattern="[0-9]{12}"
                               placeholder="12 digit Aadhar number" required>
                        <div class="field-error d-none" id="aadhar-error">Aadhar number must be exactly 12 digits.</div>
                        @if($err('aadhar_number'))<div class="field-error">{{ $err('aadhar_number') }}</div>@endif
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address <span class="req">*</span></label>
                        <textarea name="address" class="form-control {{ $err('address') ? 'is-invalid' : '' }}" rows="2" required>{{ $v('address') }}</textarea>
                        @if($err('address'))<div class="field-error">{{ $err('address') }}</div>@endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State <span class="req">*</span></label>
                        <select name="state_id" id="state_id" class="form-select {{ $err('state_id') ? 'is-invalid' : '' }}" required>
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ (string)$v('state_id') === (string)$state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($err('state_id'))<div class="field-error">{{ $err('state_id') }}</div>@endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">District <span class="req">*</span></label>
                        <select name="district_id" id="district_id" class="form-select {{ $err('district_id') ? 'is-invalid' : '' }}" required>
                            <option value="">Select District</option>
                        </select>
                        @if($err('district_id'))<div class="field-error">{{ $err('district_id') }}</div>@endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Block <span class="req">*</span></label>
                        <select name="block" id="block" class="form-select {{ $err('block') ? 'is-invalid' : '' }}" required>
                            <option value="">Select Block</option>
                        </select>
                        @if($err('block'))<div class="field-error">{{ $err('block') }}</div>@endif
                    </div>
                </div>
                </div>

                <div class="form-section other">
                <div class="section-title">Other Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Expertise (Martial Art Type) <span class="req">*</span></label>
                        <input type="text" name="martial_art_type" class="form-control {{ $err('martial_art_type') ? 'is-invalid' : '' }}" value="{{ $v('martial_art_type') }}" placeholder="e.g. Karate, Taekwondo" required>
                        @if($err('martial_art_type'))<div class="field-error">{{ $err('martial_art_type') }}</div>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Blood Group <span class="req">*</span></label>
                        <select name="blood_group" class="form-select {{ $err('blood_group') ? 'is-invalid' : '' }}" required>
                            <option value="">Select</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                <option value="{{ $bg }}" {{ $v('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                        @if($err('blood_group'))<div class="field-error">{{ $err('blood_group') }}</div>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Reference By <span class="req">*</span></label>
                        <select name="reference_by" id="reference_by" class="form-select {{ $err('reference_by') ? 'is-invalid' : '' }}" required>
                            <option value="">Select District first</option>
                        </select>
                        @if($err('reference_by'))<div class="field-error">{{ $err('reference_by') }}</div>@endif
                    </div>
                    <div class="col-12">
                        <label class="form-label">Comment</label>
                        <textarea name="comment" class="form-control {{ $err('comment') ? 'is-invalid' : '' }}" rows="2">{{ $v('comment') }}</textarea>
                        @if($err('comment'))<div class="field-error">{{ $err('comment') }}</div>@endif
                    </div>
                </div>
                </div>

                <div class="form-section docs">
                <div class="section-title">Documents</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Aadhar {{ $hasDoc('aadhar_doc') ? '(optional — already saved)' : '' }} <span class="req">{{ $hasDoc('aadhar_doc') ? '' : '*' }}</span></label>
                        <input type="file" name="aadhar_doc" class="form-control doc-file {{ $err('aadhar_doc') ? 'is-invalid' : '' }}" data-max-mb="5" data-types="jpg,jpeg,png,pdf" accept=".jpg,.jpeg,.png,.pdf" {{ $hasDoc('aadhar_doc') ? '' : 'required' }}>
                        <div class="field-error file-error d-none"></div>
                        @if($err('aadhar_doc'))<div class="field-error">{{ $err('aadhar_doc') }}</div>@endif
                        @if(!empty($draftDocs['aadhar_doc']['name']))
                            <div class="current-file mt-1 text-success">Saved: {{ $draftDocs['aadhar_doc']['name'] }}</div>
                        @elseif($r && $r->aadhar_doc)
                            <div class="current-file mt-1"><a href="{{ url('/m/r/'.basename($r->aadhar_doc)) }}" target="_blank">View current Aadhar</a></div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Qualification {{ $hasDoc('qualification_doc') ? '(optional — already saved)' : '' }} <span class="req">{{ $hasDoc('qualification_doc') ? '' : '*' }}</span></label>
                        <input type="file" name="qualification_doc" class="form-control doc-file {{ $err('qualification_doc') ? 'is-invalid' : '' }}" data-max-mb="5" data-types="jpg,jpeg,png,pdf" accept=".jpg,.jpeg,.png,.pdf" {{ $hasDoc('qualification_doc') ? '' : 'required' }}>
                        <div class="field-error file-error d-none"></div>
                        @if($err('qualification_doc'))<div class="field-error">{{ $err('qualification_doc') }}</div>@endif
                        @if(!empty($draftDocs['qualification_doc']['name']))
                            <div class="current-file mt-1 text-success">Saved: {{ $draftDocs['qualification_doc']['name'] }}</div>
                        @elseif($r && $r->qualification_doc)
                            <div class="current-file mt-1"><a href="{{ url('/m/r/'.basename($r->qualification_doc)) }}" target="_blank">View current Qualification</a></div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Martial Art Certificate {{ $hasDoc('martial_art_doc') ? '(optional — already saved)' : '' }} <span class="req">{{ $hasDoc('martial_art_doc') ? '' : '*' }}</span></label>
                        <input type="file" name="martial_art_doc" class="form-control doc-file {{ $err('martial_art_doc') ? 'is-invalid' : '' }}" data-max-mb="5" data-types="jpg,jpeg,png,pdf" accept=".jpg,.jpeg,.png,.pdf" {{ $hasDoc('martial_art_doc') ? '' : 'required' }}>
                        <div class="field-error file-error d-none"></div>
                        @if($err('martial_art_doc'))<div class="field-error">{{ $err('martial_art_doc') }}</div>@endif
                        @if(!empty($draftDocs['martial_art_doc']['name']))
                            <div class="current-file mt-1 text-success">Saved: {{ $draftDocs['martial_art_doc']['name'] }}</div>
                        @elseif($r && $r->martial_art_doc)
                            <div class="current-file mt-1"><a href="{{ url('/m/r/'.basename($r->martial_art_doc)) }}" target="_blank">View current Martial Art Doc</a></div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo {{ $hasDoc('photo') ? '(optional — already saved)' : '' }} <span class="req">{{ $hasDoc('photo') ? '' : '*' }}</span></label>
                        <input type="file" name="photo" class="form-control doc-file {{ $err('photo') ? 'is-invalid' : '' }}" data-max-mb="3" data-types="jpg,jpeg,png" accept=".jpg,.jpeg,.png" {{ $hasDoc('photo') ? '' : 'required' }}>
                        <div class="field-error file-error d-none"></div>
                        @if($err('photo'))<div class="field-error">{{ $err('photo') }}</div>@endif
                        @if(!empty($draftDocs['photo']['name']))
                            <div class="current-file mt-1 text-success">Saved: {{ $draftDocs['photo']['name'] }}</div>
                        @elseif($r && $r->photo)
                            <div class="current-file mt-1"><a href="{{ url('/m/r/'.basename($r->photo)) }}" target="_blank">View current Photo</a></div>
                        @endif
                    </div>
                </div>
                </div>

                <div class="form-section terms">
                    <div class="form-check">
                        <input class="form-check-input {{ $err('terms_accepted') ? 'is-invalid' : '' }}"
                               type="checkbox"
                               name="terms_accepted"
                               id="terms_accepted"
                               value="1"
                               {{ old('terms_accepted') ? 'checked' : '' }}
                               {{ old('terms_accepted') ? '' : 'disabled' }}
                               required>
                        <label class="form-check-label" for="terms_accepted">
                            I have read and agree to the
                            <a href="javascript:void(0)" id="open_tc_modal" class="tc-link">Terms &amp; Conditions / Acknowledgement</a>
                            <span class="req">*</span>
                        </label>
                    </div>
                    @if($err('terms_accepted'))
                        <div class="field-error">{{ $err('terms_accepted') }}</div>
                    @endif
                    <div class="field-error d-none" id="terms-error">Please read and accept Terms &amp; Conditions to submit.</div>
                </div>

                <div class="form-actions">
                    <a href="{{ url('/login') }}">Already have an account? Login</a>
                    <button type="submit" class="btn btn-submit">
                        {{ !empty($isEdit) ? 'Resubmit Corrected Form' : 'Submit Registration' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- T&C Modal --}}
<div class="tc-modal-backdrop" id="tc_modal">
    <div class="tc-modal">
        <div class="tc-modal-header">
            <strong>Terms &amp; Conditions / Acknowledgement</strong>
            <button type="button" class="btn btn-sm btn-light" id="close_tc_modal">Close</button>
        </div>
        <div class="tc-modal-body" id="tc_modal_body">
            <h5 class="mb-3">UNDERTAKING / DECLARATION</h5>
            <p>I hereby declare that I am submitting this undertaking voluntarily and with full consciousness, without any pressure or coercion.</p>
            <p>I undertake to deliver the highest quality of training to the best of my knowledge, skills, and experience. I shall perform my duties honestly and strictly in accordance with the guidelines, rules, and conditions prescribed by Samagra Shiksha, Haryana and Sane Overseas Private Limited.</p>
            <p>I agree to follow all instructions, Standard Operating Procedures (SOPs), and directions issued by Sane Overseas Private Limited from time to time. I understand that any negligence, misconduct, violation of rules, or failure to perform my assigned responsibilities may result in disciplinary, contractual, or legal action by Sane Overseas Private Limited.</p>
            <p>I undertake to successfully conduct the Rani Laxmi Bai Atam Raksha Prashikshan (Self-Defence Training) in every school allotted to me and ensure completion of the prescribed minimum 60 hours of training in each school, unless otherwise directed by the competent authority.</p>
            <p>I shall maintain professional conduct and respectful behaviour towards students, teachers, school authorities, Government officials, and all stakeholders. I shall not consume or be under the influence of alcohol, drugs, or any intoxicating substance while performing my duties.</p>
            <p>I further declare that I shall not indulge in any form of misbehaviour, harassment, discrimination, abuse, or inappropriate conduct with any student, teacher, or staff member during the training programme. I understand that such misconduct shall make me personally liable for disciplinary and legal action.</p>
            <p>I confirm that I possess adequate proficiency in at least one recognized martial art discipline required for self-defence training. I shall maintain good physical fitness and sound mental health throughout the training period.</p>
            <p>I further declare that no criminal case, sexual harassment case, or any offence involving moral turpitude is pending against me, nor have I ever been convicted of any such offence.</p>
            <p>I certify that all information and documents submitted by me are true and correct. If any information is found to be false or misleading, my engagement may be terminated immediately without notice, and appropriate legal action may be initiated against me.</p>
            <p>By submitting this form, I confirm that I have read, understood, and accepted all the above terms and conditions and agree to abide by them.</p>

            <hr class="my-4">

            <h5 class="mb-3">घोषणा / शपथ-पत्र</h5>
            <p>मैं यह घोषणा करता/करती हूँ कि मैं यह घोषणा अपनी स्वेच्छा से, पूर्ण होश-हवास एवं बिना किसी दबाव के प्रस्तुत कर रहा/रही हूँ।</p>
            <p>मैं अपने ज्ञान, अनुभव एवं कौशल के अनुसार सर्वोत्तम गुणवत्ता का प्रशिक्षण प्रदान करने का संकल्प लेता/लेती हूँ तथा समग्र शिक्षा, हरियाणा एवं Sane Overseas Private Limited द्वारा निर्धारित सभी नियमों, शर्तों, दिशा-निर्देशों एवं SOP का पूर्णतः पालन करूँगा/करूँगी।</p>
            <p>मैं Sane Overseas Private Limited द्वारा समय-समय पर जारी सभी निर्देशों का पालन करूँगा/करूँगी। यदि मेरे द्वारा किसी प्रकार की लापरवाही, अनुशासनहीनता, नियमों का उल्लंघन अथवा कर्तव्य में चूक पाई जाती है, तो मेरे विरुद्ध Sane Overseas Private Limited आवश्यक अनुशासनात्मक, संविदात्मक अथवा कानूनी कार्यवाही करने के लिए पूर्णतः अधिकृत होगी।</p>
            <p>मैं यह सुनिश्चित करूँगा/करूँगी कि समग्र शिक्षा, हरियाणा के अंतर्गत रानी लक्ष्मीबाई आत्म रक्षा प्रशिक्षण (Self-Defence Training) प्रत्येक आवंटित विद्यालय में सफलतापूर्वक संचालित किया जाएगा तथा प्रत्येक विद्यालय में निर्धारित न्यूनतम 60 घंटे का प्रशिक्षण पूर्ण कराया जाएगा।</p>
            <p>मैं प्रशिक्षण अवधि के दौरान विद्यार्थियों, शिक्षकों, विद्यालय प्रशासन, सरकारी अधिकारियों एवं अन्य संबंधित व्यक्तियों के प्रति सदैव सम्मानजनक एवं अनुकरणीय व्यवहार रखूँगा/रखूँगी। मैं किसी भी प्रकार के नशीले पदार्थ, शराब या अन्य मादक पदार्थ के प्रभाव में प्रशिक्षण नहीं दूँगा/दूँगी।</p>
            <p>मैं किसी भी छात्र/छात्रा, शिक्षक अथवा विद्यालय कर्मचारी के साथ किसी प्रकार का दुर्व्यवहार, उत्पीड़न, यौन उत्पीड़न, अभद्र व्यवहार या अनुचित आचरण नहीं करूँगा/करूँगी। ऐसा पाए जाने पर मेरे विरुद्ध विधिसम्मत अनुशासनात्मक एवं कानूनी कार्यवाही की जा सकती है।</p>
            <p>मैं यह भी घोषित करता/करती हूँ कि आत्मरक्षा प्रशिक्षण हेतु मेरे पास कम-से-कम एक मान्यता प्राप्त मार्शल आर्ट में आवश्यक दक्षता है तथा प्रशिक्षण अवधि के दौरान मैं अपना शारीरिक एवं मानसिक स्वास्थ्य उत्तम बनाए रखूँगा/रखूँगी।</p>
            <p>मैं यह घोषित करता/करती हूँ कि मेरे विरुद्ध कोई आपराधिक मामला, यौन उत्पीड़न का मामला अथवा नैतिक अधमता से संबंधित कोई प्रकरण लंबित नहीं है और न ही मुझे किसी ऐसे अपराध में दोषी ठहराया गया है।</p>
            <p>मैं प्रमाणित करता/करती हूँ कि मेरे द्वारा दी गई सभी जानकारी एवं दस्तावेज सत्य एवं सही हैं। यदि भविष्य में कोई जानकारी असत्य या भ्रामक पाई जाती है, तो मेरी सेवाएँ तत्काल समाप्त की जा सकती हैं तथा मेरे विरुद्ध आवश्यक कानूनी कार्यवाही की जा सकती है।</p>
            <p><strong>इस फॉर्म को सबमिट करके मैं पुष्टि करता/करती हूँ कि मैंने उपरोक्त सभी नियम एवं शर्तें पढ़ ली हैं, उन्हें समझ लिया है तथा उनका पूर्णतः पालन करने के लिए सहमत हूँ।</strong></p>
        </div>
        <div class="tc-modal-footer">
            <span class="tc-hint" id="tc_scroll_hint">Please scroll to the end to enable Agree.</span>
            <button type="button" class="btn btn-primary" id="tc_agree_btn" disabled>I Agree</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 2500
    });
    @endif

    // Phone: only 10 digits
    const $phone = $('#instructor_number');
    const $phoneError = $('#phone-error');

    function validatePhone() {
        const value = $phone.val().replace(/\D/g, '');
        $phone.val(value);
        if (value.length === 0) {
            $phone.removeClass('is-invalid');
            $phoneError.addClass('d-none');
            return false;
        }
        if (value.length !== 10) {
            $phone.addClass('is-invalid');
            $phoneError.removeClass('d-none');
            return false;
        }
        $phone.removeClass('is-invalid');
        $phoneError.addClass('d-none');
        return true;
    }

    $phone.on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
        validatePhone();
    });

    // Aadhar: only 12 digits
    const $aadhar = $('#aadhar_number');
    const $aadharError = $('#aadhar-error');

    function validateAadhar() {
        const value = $aadhar.val().replace(/\D/g, '');
        $aadhar.val(value);
        if (value.length === 0) {
            $aadhar.removeClass('is-invalid');
            $aadharError.addClass('d-none');
            return false;
        }
        if (value.length !== 12) {
            $aadhar.addClass('is-invalid');
            $aadharError.removeClass('d-none');
            return false;
        }
        $aadhar.removeClass('is-invalid');
        $aadharError.addClass('d-none');
        return true;
    }

    $aadhar.on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 12);
        validateAadhar();
    });

    // File size + type: show error only under that file field
    function validateFileInput(input) {
        const $input = $(input);
        const $error = $input.siblings('.file-error');
        const maxMb = parseFloat($input.data('max-mb')) || 5;
        const maxBytes = maxMb * 1024 * 1024;
        const allowed = String($input.data('types') || 'jpg,jpeg,png,pdf').split(',').map(function (t) {
            return t.trim().toLowerCase();
        });
        const file = input.files && input.files[0];

        if (!file) {
            $input.removeClass('is-invalid');
            $error.addClass('d-none').text('');
            return true;
        }

        const ext = file.name.split('.').pop().toLowerCase();
        if (allowed.indexOf(ext) === -1) {
            $input.addClass('is-invalid');
            $error
                .text('Invalid file type. Allowed: ' + allowed.join(', ').toUpperCase())
                .removeClass('d-none');
            $input.val('');
            return false;
        }

        if (file.size > maxBytes) {
            $input.addClass('is-invalid');
            $error
                .text('File is too large. Maximum allowed is ' + maxMb + ' MB.')
                .removeClass('d-none');
            $input.val('');
            return false;
        }

        $input.removeClass('is-invalid');
        $error.addClass('d-none').text('');
        return true;
    }

    $('.doc-file').on('change', function () {
        validateFileInput(this);
    });

    // Terms & Conditions modal + scroll lock
    let tcScrolledToEnd = {{ old('terms_accepted') ? 'true' : 'false' }};
    const $tcModal = $('#tc_modal');
    const $tcBody = $('#tc_modal_body');
    const $tcAgreeBtn = $('#tc_agree_btn');
    const $terms = $('#terms_accepted');
    const $termsError = $('#terms-error');

    @if(old('terms_accepted'))
    $terms.prop('disabled', false).prop('checked', true);
    $tcAgreeBtn.prop('disabled', false);
    $('#tc_scroll_hint').text('Already accepted. You can submit, or reopen T&C if needed.');
    @endif

    function openTcModal() {
        $tcModal.addClass('show');
        $tcBody.scrollTop(0);
        checkTcScroll();
    }

    function closeTcModal() {
        $tcModal.removeClass('show');
    }

    function checkTcScroll() {
        const el = $tcBody.get(0);
        if (!el) return;
        const reachedEnd = el.scrollTop + el.clientHeight >= el.scrollHeight - 8;
        // If content is short and no scroll needed, treat as readable
        const noScrollNeeded = el.scrollHeight <= el.clientHeight + 8;
        tcScrolledToEnd = reachedEnd || noScrollNeeded;
        $tcAgreeBtn.prop('disabled', !tcScrolledToEnd);
        $('#tc_scroll_hint').text(
            tcScrolledToEnd
                ? ''
                : 'Please scroll to the end to enable Agree.'
        );
    }

    $('#open_tc_modal').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openTcModal();
    });

    // Prevent checking checkbox until T&C scrolled/agreed
    $terms.on('click', function (e) {
        if ($terms.prop('disabled') || !tcScrolledToEnd) {
            e.preventDefault();
            openTcModal();
            $termsError.removeClass('d-none').text('Please open T&C, scroll to the end, then click I Agree.');
        }
    });

    // Label click on non-link text should not auto-check disabled box oddly
    $('label[for="terms_accepted"]').on('click', function (e) {
        if ($(e.target).is('#open_tc_modal') || $(e.target).closest('#open_tc_modal').length) {
            return;
        }
        if ($terms.prop('disabled')) {
            e.preventDefault();
            openTcModal();
        }
    });

    $tcBody.on('scroll', checkTcScroll);
    $('#close_tc_modal').on('click', closeTcModal);
    $tcModal.on('click', function (e) {
        if (e.target === this) closeTcModal();
    });

    $tcAgreeBtn.on('click', function () {
        if (!tcScrolledToEnd) return;
        $terms.prop('disabled', false).prop('checked', true).removeClass('is-invalid');
        $termsError.addClass('d-none');
        closeTcModal();
    });

    $('#registrationForm').on('submit', function (e) {
        let ok = validatePhone() && validateAadhar();
        $('.doc-file').each(function () {
            if (!validateFileInput(this)) {
                ok = false;
            }
        });

        // disabled checkbox is not submitted — enable briefly if checked
        if ($terms.is(':checked')) {
            $terms.prop('disabled', false);
        }

        if (!$terms.is(':checked')) {
            $terms.addClass('is-invalid');
            $termsError.removeClass('d-none').text('Please read and accept Terms & Conditions to submit.');
            ok = false;
        } else {
            $terms.removeClass('is-invalid');
            $termsError.addClass('d-none');
        }

        if (!ok) {
            e.preventDefault();
            const $first = $('.is-invalid').first();
            if ($first.length) {
                $('html, body').animate({ scrollTop: $first.offset().top - 80 }, 300);
            }
        }
    });

    @if($errors->any())
    $(function () {
        const $first = $('.is-invalid, .field-error').filter(function () {
            return $(this).hasClass('is-invalid') || ($(this).text() || '').trim().length;
        }).first();
        if ($first.length) {
            $('html, body').animate({ scrollTop: $first.offset().top - 80 }, 300);
        }
    });
    @endif

    const selectedDistrictId = @json(old('district_id', $r?->district_id ?? null));
    const selectedDistrictName = @json(old('district', $r?->district ?? null));
    const selectedBlock = @json(old('block', $r?->block ?? null));
    const selectedReference = @json(old('reference_by', $r?->reference_cordinator_id ?? null));

    function loadCoordinators(districtId, selected) {
        const $ref = $('#reference_by');
        $ref.html('<option value="">Select Coordinator</option>');
        if (!districtId) {
            $ref.html('<option value="">Select District first</option>');
            return;
        }

        $.get('{{ url('/trainer-register/coordinators') }}/' + districtId, function (data) {
            if (!data.length) {
                $ref.html('<option value="">No coordinator found</option>');
                return;
            }
            data.forEach(function (item) {
                const id = item.cordinator_id;
                const name = item.instructor_name;
                if (!id) return;
                const isSelected = selected && String(selected) === String(id) ? 'selected' : '';
                $ref.append('<option value="' + id + '" ' + isSelected + '>' + name + '</option>');
            });
        });
    }

    function loadBlocks(districtId, selected) {
        const $block = $('#block');
        $block.html('<option value="">Select Block</option>');
        if (!districtId) return;

        $.get('{{ url('/trainer-register/blocks') }}/' + districtId, function (data) {
            data.forEach(function (item) {
                const isSelected = selected && selected === item.block ? 'selected' : '';
                $block.append('<option value="' + item.block + '" ' + isSelected + '>' + item.block + '</option>');
            });
        });
    }

    function loadDistricts(stateId, selectedId, selectedName, selectedBlockName, selectedRef) {
        const $district = $('#district_id');
        $district.html('<option value="">Select District</option>');
        $('#block').html('<option value="">Select Block</option>');
        $('#reference_by').html('<option value="">Select District first</option>');
        if (!stateId) return;

        $.get('{{ url('/trainer-register/districts') }}/' + stateId, function (data) {
            let matchedId = selectedId;
            data.forEach(function (item) {
                if (!matchedId && selectedName && selectedName === item.district) {
                    matchedId = item.id;
                }
                const isSelected = matchedId && String(matchedId) === String(item.id) ? 'selected' : '';
                $district.append('<option value="' + item.id + '" ' + isSelected + '>' + item.district + '</option>');
            });

            if (matchedId) {
                loadBlocks(matchedId, selectedBlockName);
                loadCoordinators(matchedId, selectedRef);
            }
        });
    }

    $('#state_id').on('change', function () {
        loadDistricts($(this).val(), null, null, null, null);
    });

    $('#district_id').on('change', function () {
        const districtId = $(this).val();
        loadBlocks(districtId, null);
        loadCoordinators(districtId, null);
    });

    const initialState = $('#state_id').val();
    if (initialState) {
        loadDistricts(initialState, selectedDistrictId, selectedDistrictName, selectedBlock, selectedReference);
    }
</script>
</body>
</html>
