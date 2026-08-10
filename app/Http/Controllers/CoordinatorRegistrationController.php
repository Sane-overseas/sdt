<?php

namespace App\Http\Controllers;

use App\Mail\CoordinatorCredentialsMail;
use App\Mail\TrainerRegistrationReceivedMail;
use App\Mail\TrainerRevisionMail;
use App\Models\Block;
use App\Models\CoordinatorRegistration;
use App\Models\Cordinator;
use App\Models\District;
use App\Models\State;
use App\Models\TrainerRegistration;
use App\Models\User;
use App\Services\CoordinatorCodeService;
use App\Services\CoordinatorScopeService;
use App\Services\IdCardGenerator;
use App\Services\StateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CoordinatorRegistrationController extends BaseController
{
    private const DRAFT_DOCS_SESSION = 'coord_reg_docs';

    private array $documentFields = [
        'aadhar_doc' => 'aadhar',
        'qualification_doc' => 'qualification',
        'martial_art_doc' => 'martial_art',
        'photo' => 'photo',
    ];

    public function showForm()
    {
        return view('coordinator.register', [
            'states' => State::where('is_active', true)->orderBy('name')->get(),
            'registration' => null,
            'isEdit' => false,
            'draftDocs' => Session::get(self::DRAFT_DOCS_SESSION, []),
        ]);
    }

    public function editForm(string $token)
    {
        $registration = CoordinatorRegistration::where('edit_token', $token)->firstOrFail();

        if (!$registration->canBeEditedByApplicant()) {
            return view('trainer.register-locked', [
                'message' => 'This registration cannot be edited right now. It is locked until admin requests a correction.',
                'registration' => $registration,
            ]);
        }

        return view('coordinator.register', [
            'states' => State::where('is_active', true)->orderBy('name')->get(),
            'registration' => $registration,
            'isEdit' => true,
            'draftDocs' => Session::get(self::DRAFT_DOCS_SESSION, []),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateRegistration($request, false);
        } catch (ValidationException $e) {
            $this->stashValidDocuments($request);
            throw $e;
        }

        $data = $this->registrationPayload($validated);
        $code = CoordinatorCodeService::next(CoordinatorScopeService::LEVEL_DISTRICT);
        $paths = $this->storeDocuments($request, null, $code);

        foreach (array_keys($this->documentFields) as $field) {
            if (empty($paths[$field])) {
                $this->stashValidDocuments($request);
                throw ValidationException::withMessages([
                    $field => ['Please upload this document.'],
                ]);
            }
        }

        $registration = CoordinatorRegistration::create(array_merge($data, $paths, [
            'instructor_code' => $code,
            'status' => CoordinatorRegistration::STATUS_PENDING,
            'edit_token' => null,
            'admin_remarks' => null,
        ]));

        $this->clearDraftDocuments();

        try {
            Mail::to($registration->email)->send(new TrainerRegistrationReceivedMail(
                $registration->instructor_name,
                $registration->instructor_code,
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('coordinator.register')
            ->with('success', 'Registered successfully. Please check your email and wait for admin approval.');
    }

    public function update(Request $request, string $token)
    {
        $registration = CoordinatorRegistration::where('edit_token', $token)->firstOrFail();

        if (!$registration->canBeEditedByApplicant()) {
            return redirect()
                ->route('coordinator.register')
                ->withErrors(['form' => 'This form is locked. You can edit only after admin sends correction remarks.']);
        }

        try {
            $validated = $this->validateRegistration($request, true, $registration);
        } catch (ValidationException $e) {
            $this->stashValidDocuments($request);
            throw $e;
        }

        $data = $this->registrationPayload($validated);
        $paths = $this->storeDocuments($request, $registration, $registration->instructor_code);

        $registration->update(array_merge($data, $paths, [
            'status' => CoordinatorRegistration::STATUS_PENDING,
            'edit_token' => null,
            'admin_remarks' => null,
            'rejection_note' => null,
        ]));

        $this->clearDraftDocuments();

        return redirect()
            ->route('coordinator.register')
            ->with('success', 'Updated successfully. Waiting for admin approval.');
    }

    public function index()
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $stateId = StateService::scopeStateId();

        $registrations = CoordinatorRegistration::with('state')
            ->when($stateId, fn ($q) => $q->where('state_id', $stateId))
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'revision' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at')
            ->get();

        return view('admin.coordinator-registrations', compact('registrations'));
    }

    public function show($id)
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $registration = CoordinatorRegistration::with('state')->findOrFail($id);
        $data = $registration->toArray();
        $data['edit_url'] = ($registration->isRevision() && $registration->edit_token)
            ? route('coordinator.register.edit', $registration->edit_token)
            : null;

        return response()->json($data);
    }

    public function approve(Request $request, $id)
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $registration = CoordinatorRegistration::findOrFail($id);

        if (!$registration->isPending()) {
            return response()->json(['message' => 'Only pending registrations can be approved.'], 422);
        }

        $validated = $request->validate([
            'password' => 'nullable|string|min:6',
        ]);

        $stateId = StateService::scopeStateId();
        if ($stateId && (int) $registration->state_id !== (int) $stateId) {
            return response()->json(['message' => 'Registration belongs to another state.'], 403);
        }

        if (empty($registration->instructor_code)) {
            return response()->json(['message' => 'Coordinator code missing on registration.'], 422);
        }

        if (User::where('instructor_code', $registration->instructor_code)->exists()
            || Cordinator::where('cordinator_code', $registration->instructor_code)->exists()) {
            return response()->json(['message' => 'Coordinator code already used.'], 422);
        }

        if (User::where('email', $registration->email)->exists()) {
            return response()->json(['message' => 'A user with this email already exists.'], 422);
        }

        $plainPassword = $validated['password'] ?: 'SOPL@1634';

        $cordinator = Cordinator::create([
            'cordinator_name' => $registration->instructor_name,
            'cordinator_code' => $registration->instructor_code,
            'state_id' => $registration->state_id,
        ]);

        $user = User::create([
            'instructor_name' => $registration->instructor_name,
            'father_name' => $registration->father_name,
            'email' => $registration->email,
            'password' => $plainPassword,
            'instructor_code' => $registration->instructor_code,
            'instructor_number' => $registration->instructor_number,
            'aadhar_number' => $registration->aadhar_number,
            'address' => $registration->address,
            'blood_group' => $registration->blood_group,
            'martial_art_type' => $registration->martial_art_type,
            'comment' => $registration->comment,
            'aadhar_doc' => $registration->aadhar_doc,
            'qualification_doc' => $registration->qualification_doc,
            'martial_art_doc' => $registration->martial_art_doc,
            'photo' => $registration->photo,
            'cordinator_id' => $cordinator->id,
            'coordinator_level' => CoordinatorScopeService::LEVEL_DISTRICT,
            'district' => $registration->district,
            'block' => $registration->block,
            'state_id' => $registration->state_id,
            'amount' => 0,
            'extra_amount' => 0,
        ]);

        $user->forceFill([
            'role' => 2,
            'active_status' => 1,
        ])->save();

        $registration->update([
            'status' => CoordinatorRegistration::STATUS_APPROVED,
            'user_id' => $user->id,
            'cordinator_id' => $cordinator->id,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'edit_token' => null,
            'admin_remarks' => null,
        ]);

        $idCardPath = null;
        try {
            $idCardPath = app(IdCardGenerator::class)->generate([
                'name' => $user->instructor_name,
                'code' => $user->instructor_code,
                'blood_group' => $user->blood_group,
                'designation' => 'DISTRICT COORDINATOR',
                'photo_path' => $user->photo,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Mail::to($user->email)->send(new CoordinatorCredentialsMail(
                $user->instructor_name,
                $user->email,
                $user->instructor_code,
                $plainPassword,
                url('/login'),
                $idCardPath,
            ));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'District coordinator approved but email could not be sent. Share credentials manually.',
                'password' => $plainPassword,
            ]);
        }

        return response()->json([
            'message' => $idCardPath
                ? 'District coordinator approved. Credentials and ID card emailed.'
                : 'District coordinator approved and credentials emailed (ID card could not be generated).',
        ]);
    }

    public function reject(Request $request, $id)
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $registration = CoordinatorRegistration::findOrFail($id);

        if (!$registration->isPending() && !$registration->isRevision()) {
            return response()->json(['message' => 'This registration is already processed.'], 422);
        }

        $validated = $request->validate([
            'rejection_note' => 'nullable|string|max:500',
        ]);

        $registration->update([
            'status' => CoordinatorRegistration::STATUS_REJECTED,
            'rejection_note' => $validated['rejection_note'] ?? null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'edit_token' => null,
        ]);

        return response()->json(['message' => 'Registration rejected.']);
    }

    public function requestRevision(Request $request, $id)
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $registration = CoordinatorRegistration::findOrFail($id);

        if (!$registration->isPending()) {
            return response()->json(['message' => 'Only pending registrations can be sent for correction.'], 422);
        }

        $validated = $request->validate([
            'admin_remarks' => 'required|string|max:1000',
        ]);

        $token = Str::random(48);

        $registration->update([
            'status' => CoordinatorRegistration::STATUS_REVISION,
            'admin_remarks' => $validated['admin_remarks'],
            'edit_token' => $token,
            'approved_by' => Auth::id(),
        ]);

        $editUrl = route('coordinator.register.edit', $token);

        try {
            Mail::to($registration->email)->send(new TrainerRevisionMail(
                $registration->instructor_name,
                $validated['admin_remarks'],
                $editUrl,
            ));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Remarks saved but email could not be sent. Share this edit link manually: '.$editUrl,
                'edit_url' => $editUrl,
            ]);
        }

        return response()->json([
            'message' => 'Correction remarks emailed. They can now edit and resubmit.',
            'edit_url' => $editUrl,
        ]);
    }

    private function validateRegistration(Request $request, bool $isUpdate, ?CoordinatorRegistration $registration = null): array
    {
        $request->merge([
            'aadhar_number' => preg_replace('/\D+/', '', (string) $request->input('aadhar_number', '')),
            'instructor_number' => preg_replace('/\D+/', '', (string) $request->input('instructor_number', '')),
        ]);

        $emailRules = [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email'),
        ];

        if ($isUpdate && $registration) {
            if ($registration->user_id) {
                $emailRules = [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($registration->user_id),
                ];
            }

            $emailRules[] = Rule::unique('coordinator_registrations', 'email')
                ->ignore($registration->id)
                ->where(fn ($q) => $q->whereIn('status', [
                    CoordinatorRegistration::STATUS_PENDING,
                    CoordinatorRegistration::STATUS_REVISION,
                ]));
        } else {
            $emailRules[] = Rule::unique('coordinator_registrations', 'email')
                ->where(fn ($q) => $q->whereIn('status', [
                    CoordinatorRegistration::STATUS_PENDING,
                    CoordinatorRegistration::STATUS_REVISION,
                ]));
        }

        $emailRules[] = Rule::unique('trainer_registrations', 'email')
            ->where(fn ($q) => $q->whereIn('status', [
                TrainerRegistration::STATUS_PENDING,
                TrainerRegistration::STATUS_REVISION,
            ]));

        $aadharRules = [
            'required',
            'digits:12',
            Rule::unique('users', 'aadhar_number'),
        ];

        if ($isUpdate && $registration) {
            $aadharRules[] = Rule::unique('coordinator_registrations', 'aadhar_number')->ignore($registration->id);
            if ($registration->user_id) {
                $aadharRules = [
                    'required',
                    'digits:12',
                    Rule::unique('users', 'aadhar_number')->ignore($registration->user_id),
                    Rule::unique('coordinator_registrations', 'aadhar_number')->ignore($registration->id),
                ];
            }
        } else {
            $aadharRules[] = Rule::unique('coordinator_registrations', 'aadhar_number');
        }

        $aadharRules[] = Rule::unique('trainer_registrations', 'aadhar_number');

        $draft = Session::get(self::DRAFT_DOCS_SESSION, []);
        $docRequired = function (string $field) use ($isUpdate, $registration, $draft) {
            if ($isUpdate && $registration && !empty($registration->{$field})) {
                return false;
            }
            if (!empty($draft[$field]['path'])) {
                return false;
            }

            return true;
        };

        return $request->validate([
            'instructor_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'email' => $emailRules,
            'instructor_number' => 'required|digits:10',
            'aadhar_number' => $aadharRules,
            'address' => 'required|string|max:1000',
            'state_id' => 'required|integer|exists:states,id',
            'district_id' => 'required|integer|exists:districts,id',
            'block' => 'required|string|max:255',
            'martial_art_type' => 'required|string|max:255',
            'blood_group' => 'required|string|max:20',
            'comment' => 'nullable|string|max:1000',
            'aadhar_doc' => [$docRequired('aadhar_doc') ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'qualification_doc' => [$docRequired('qualification_doc') ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'martial_art_doc' => [$docRequired('martial_art_doc') ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'photo' => [$docRequired('photo') ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png', 'max:3072'],
            'terms_accepted' => $isUpdate ? 'nullable' : 'accepted',
        ]);
    }

    private function registrationPayload(array $validated): array
    {
        $district = District::findOrFail($validated['district_id']);
        if ((int) $district->state_id !== (int) $validated['state_id']) {
            throw ValidationException::withMessages([
                'district_id' => ['Selected district does not belong to the chosen state.'],
            ]);
        }

        $blockName = trim((string) $validated['block']);
        $blockOk = Block::where('district_id', $district->id)
            ->get()
            ->contains(fn ($b) => mb_strtolower(trim((string) $b->block)) === mb_strtolower($blockName));

        if (!$blockOk) {
            throw ValidationException::withMessages([
                'block' => ['Selected block is invalid for this district.'],
            ]);
        }

        return [
            'instructor_name' => $validated['instructor_name'],
            'father_name' => $validated['father_name'],
            'email' => $validated['email'],
            'instructor_number' => $validated['instructor_number'],
            'aadhar_number' => $validated['aadhar_number'],
            'address' => $validated['address'],
            'state_id' => (int) $validated['state_id'],
            'district' => $district->district,
            'block' => $blockName,
            'martial_art_type' => $validated['martial_art_type'],
            'blood_group' => $validated['blood_group'],
            'comment' => $validated['comment'] ?? null,
        ];
    }

    private function storeDocuments(Request $request, ?CoordinatorRegistration $existing = null, ?string $code = null): array
    {
        $paths = [];
        $code = $code ?: ($existing->instructor_code ?? null);
        $draft = Session::get(self::DRAFT_DOCS_SESSION, []);

        if (!$code) {
            return $paths;
        }

        $safeCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $code);

        foreach ($this->documentFields as $field => $suffix) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $ext = strtolower($file->getClientOriginalExtension());
                $name = $safeCode.'_'.$suffix.'.'.$ext;

                if ($existing && $existing->{$field}) {
                    Storage::disk('public')->delete($existing->{$field});
                }

                $paths[$field] = $file->storeAs('coordinator_data', $name, 'public');
                continue;
            }

            if (!empty($draft[$field]['path']) && Storage::disk('public')->exists($draft[$field]['path'])) {
                $ext = pathinfo($draft[$field]['path'], PATHINFO_EXTENSION);
                $name = $safeCode.'_'.$suffix.'.'.$ext;
                $final = 'coordinator_data/'.$name;

                if ($existing && $existing->{$field}) {
                    Storage::disk('public')->delete($existing->{$field});
                }
                if (Storage::disk('public')->exists($final)) {
                    Storage::disk('public')->delete($final);
                }

                Storage::disk('public')->move($draft[$field]['path'], $final);
                $paths[$field] = $final;
                continue;
            }

            if ($existing && !empty($existing->{$field})) {
                $paths[$field] = $existing->{$field};
            }
        }

        return $paths;
    }

    private function stashValidDocuments(Request $request): void
    {
        $draft = Session::get(self::DRAFT_DOCS_SESSION, []);

        $rules = [
            'aadhar_doc' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'qualification_doc' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'martial_art_doc' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'photo' => ['file', 'mimes:jpg,jpeg,png', 'max:3072'],
        ];

        foreach ($this->documentFields as $field => $suffix) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $validator = Validator::make(
                [$field => $request->file($field)],
                [$field => $rules[$field]]
            );

            if ($validator->fails()) {
                continue;
            }

            if (!empty($draft[$field]['path'])) {
                Storage::disk('public')->delete($draft[$field]['path']);
            }

            $file = $request->file($field);
            $ext = strtolower($file->getClientOriginalExtension());
            $tmpName = 'draft_'.Str::uuid().'_'.$suffix.'.'.$ext;
            $path = $file->storeAs('coordinator_data/tmp', $tmpName, 'public');

            $draft[$field] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
            ];
        }

        Session::put(self::DRAFT_DOCS_SESSION, $draft);
    }

    private function clearDraftDocuments(): void
    {
        $draft = Session::get(self::DRAFT_DOCS_SESSION, []);
        foreach ($draft as $item) {
            if (!empty($item['path'])) {
                Storage::disk('public')->delete($item['path']);
            }
        }
        Session::forget(self::DRAFT_DOCS_SESSION);
    }
}
