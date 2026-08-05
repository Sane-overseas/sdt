<?php

namespace App\Http\Controllers;

use App\Mail\TrainerCredentialsMail;
use App\Mail\TrainerRevisionMail;
use App\Models\Block;
use App\Models\Cordinator;
use App\Models\District;
use App\Models\State;
use App\Models\TrainerRegistration;
use App\Models\User;
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

class TrainerRegistrationController extends BaseController
{
    private const DRAFT_DOCS_SESSION = 'trainer_reg_docs';

    private array $documentFields = [
        'aadhar_doc' => 'aadhar',
        'qualification_doc' => 'qualification',
        'martial_art_doc' => 'martial_art',
        'photo' => 'photo',
    ];

    public function showForm()
    {
        $states = State::where('is_active', true)->orderBy('name')->get();

        return view('trainer.register', [
            'states' => $states,
            'registration' => null,
            'isEdit' => false,
            'draftDocs' => Session::get(self::DRAFT_DOCS_SESSION, []),
        ]);
    }

    public function editForm(string $token)
    {
        $registration = TrainerRegistration::where('edit_token', $token)->firstOrFail();

        if (!$registration->canBeEditedByTrainer()) {
            return view('trainer.register-locked', [
                'message' => 'This registration cannot be edited right now. It is locked until admin requests a correction.',
                'registration' => $registration,
            ]);
        }

        $states = State::where('is_active', true)->orderBy('name')->get();

        return view('trainer.register', [
            'states' => $states,
            'registration' => $registration,
            'isEdit' => true,
            'draftDocs' => Session::get(self::DRAFT_DOCS_SESSION, []),
        ]);
    }

    public function districtsByState($stateId)
    {
        $districts = District::where('state_id', $stateId)
            ->orderBy('district')
            ->get(['id', 'district']);

        return response()->json($districts);
    }

    public function blocksByDistrict($districtId)
    {
        $blocks = Block::where('district_id', $districtId)
            ->orderBy('block')
            ->get(['id', 'block']);

        return response()->json($blocks);
    }

    public function coordinatorsByDistrict($districtId)
    {
        $district = District::findOrFail($districtId);

        $coordinators = User::where('role', 2)
            ->where('state_id', $district->state_id)
            ->where('district', $district->district)
            ->orderBy('instructor_name')
            ->get(['id', 'instructor_name', 'cordinator_id']);

        if ($coordinators->isEmpty()) {
            $coordinators = Cordinator::where('state_id', $district->state_id)
                ->orderBy('cordinator_name')
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'instructor_name' => $c->cordinator_name,
                    'cordinator_id' => $c->id,
                ]);
        } else {
            $coordinators = $coordinators->map(fn ($u) => [
                'id' => $u->id,
                'instructor_name' => $u->instructor_name,
                'cordinator_id' => $u->cordinator_id ?: $u->id,
            ])->filter(fn ($c) => !empty($c['cordinator_id']))->values();
        }

        return response()->json($coordinators);
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
        $instructorCode = $this->generateInstructorCode((int) $data['state_id']);
        $paths = $this->storeDocuments($request, null, $instructorCode);

        foreach (array_keys($this->documentFields) as $field) {
            if (empty($paths[$field])) {
                $this->stashValidDocuments($request);
                throw ValidationException::withMessages([
                    $field => ['Please upload this document.'],
                ]);
            }
        }

        TrainerRegistration::create(array_merge($data, $paths, [
            'instructor_code' => $instructorCode,
            'status' => TrainerRegistration::STATUS_PENDING,
            'edit_token' => null,
            'admin_remarks' => null,
        ]));

        $this->clearDraftDocuments();

        return redirect()
            ->route('trainer.register')
            ->with('success', 'Registered successfully');
    }

    public function update(Request $request, string $token)
    {
        $registration = TrainerRegistration::where('edit_token', $token)->firstOrFail();

        if (!$registration->canBeEditedByTrainer()) {
            return redirect()
                ->route('trainer.register')
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
            'status' => TrainerRegistration::STATUS_PENDING,
            'edit_token' => null,
            'admin_remarks' => null,
            'rejection_note' => null,
        ]));

        $this->clearDraftDocuments();

        return redirect()
            ->route('trainer.register')
            ->with('success', 'Registered successfully');
    }

    public function index()
    {
        $stateId = StateService::scopeStateId();

        $registrations = TrainerRegistration::with('state')
            ->when($stateId, fn ($q) => $q->where('state_id', $stateId))
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'revision' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at')
            ->get();

        $cordinator = StateService::cordinatorsQuery()->orderBy('cordinator_name')->get();

        return view('admin.trainer-registrations', compact('registrations', 'cordinator'));
    }

    public function show($id)
    {
        $registration = TrainerRegistration::with('state')->findOrFail($id);

        return response()->json($registration);
    }

    public function approve(Request $request, $id)
    {
        $registration = TrainerRegistration::findOrFail($id);

        if (!$registration->isPending()) {
            return response()->json(['message' => 'Only pending registrations can be approved.'], 422);
        }

        $validated = $request->validate([
            'cordinator' => 'required|integer',
            'amount' => 'nullable|numeric',
            'extra_amount' => 'nullable|numeric',
            'password' => 'nullable|string|min:6',
        ]);

        if (empty($registration->instructor_code)) {
            return response()->json(['message' => 'Instructor code missing on registration.'], 422);
        }

        if (User::where('instructor_code', $registration->instructor_code)->exists()) {
            return response()->json(['message' => 'Instructor code already used by another user.'], 422);
        }

        if (User::where('email', $registration->email)->exists()) {
            return response()->json(['message' => 'A user with this email already exists.'], 422);
        }

        StateService::assertCordinatorInScope((int) $validated['cordinator']);

        $plainPassword = $validated['password'] ?: 'SOPL@1634';

        $trainer = User::create([
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
            'cordinator_id' => $validated['cordinator'],
            'amount' => $validated['amount'] ?? 0,
            'extra_amount' => $validated['extra_amount'] ?? 0,
            'district' => $registration->district,
            'block' => $registration->block,
            'state_id' => $registration->state_id,
        ]);

        $trainer->forceFill([
            'role' => 0,
            'active_status' => 1,
        ])->save();

        $registration->update([
            'status' => TrainerRegistration::STATUS_APPROVED,
            'user_id' => $trainer->id,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'edit_token' => null,
            'admin_remarks' => null,
        ]);

        $idCardPath = null;
        try {
            $idCardPath = app(IdCardGenerator::class)->generate([
                'name' => $registration->instructor_name,
                'code' => $registration->instructor_code,
                'blood_group' => $registration->blood_group,
                'designation' => 'TRAINER',
                'photo_path' => $registration->photo,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Mail::to($trainer->email)->send(new TrainerCredentialsMail(
                $trainer->instructor_name,
                $trainer->email,
                $trainer->instructor_code,
                $plainPassword,
                url('/login'),
                $idCardPath,
            ));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Trainer approved but email could not be sent. Please share credentials manually.',
                'trainer' => $trainer,
                'password' => $plainPassword,
                'id_card' => $idCardPath,
            ]);
        }

        return response()->json([
            'message' => $idCardPath
                ? 'Trainer approved. Credentials and ID card emailed successfully.'
                : 'Trainer approved and credentials emailed (ID card could not be generated).',
            'trainer' => $trainer,
            'id_card' => $idCardPath,
        ]);
    }

    public function reject(Request $request, $id)
    {
        $registration = TrainerRegistration::findOrFail($id);

        if (!$registration->isPending() && !$registration->isRevision()) {
            return response()->json(['message' => 'This registration is already processed.'], 422);
        }

        $validated = $request->validate([
            'rejection_note' => 'nullable|string|max:500',
        ]);

        $registration->update([
            'status' => TrainerRegistration::STATUS_REJECTED,
            'rejection_note' => $validated['rejection_note'] ?? null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'edit_token' => null,
        ]);

        return response()->json(['message' => 'Registration rejected.']);
    }

    public function requestRevision(Request $request, $id)
    {
        $registration = TrainerRegistration::findOrFail($id);

        if (!$registration->isPending()) {
            return response()->json(['message' => 'Only pending registrations can be sent for correction.'], 422);
        }

        $validated = $request->validate([
            'admin_remarks' => 'required|string|max:1000',
        ]);

        $token = Str::random(48);

        $registration->update([
            'status' => TrainerRegistration::STATUS_REVISION,
            'admin_remarks' => $validated['admin_remarks'],
            'edit_token' => $token,
            'approved_by' => Auth::id(),
        ]);

        $editUrl = route('trainer.register.edit', $token);

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
            'message' => 'Correction remarks emailed to trainer. They can now edit and resubmit.',
        ]);
    }

    private function validateRegistration(Request $request, bool $isUpdate, ?TrainerRegistration $registration = null): array
    {
        $emailRules = [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email'),
        ];

        if ($isUpdate && $registration) {
            $emailRules[] = Rule::unique('trainer_registrations', 'email')
                ->ignore($registration->id)
                ->where(fn ($q) => $q->whereIn('status', [
                    TrainerRegistration::STATUS_PENDING,
                    TrainerRegistration::STATUS_REVISION,
                ]));
        } else {
            $emailRules[] = Rule::unique('trainer_registrations', 'email')
                ->where(fn ($q) => $q->whereIn('status', [
                    TrainerRegistration::STATUS_PENDING,
                    TrainerRegistration::STATUS_REVISION,
                ]));
        }

        $draft = Session::get(self::DRAFT_DOCS_SESSION, []);

        $aadharRule = ($isUpdate || !empty($draft['aadhar_doc']['path'])) ? 'nullable' : 'required';
        $qualificationRule = ($isUpdate || !empty($draft['qualification_doc']['path'])) ? 'nullable' : 'required';
        $martialRule = ($isUpdate || !empty($draft['martial_art_doc']['path'])) ? 'nullable' : 'required';
        $photoRule = ($isUpdate || !empty($draft['photo']['path'])) ? 'nullable' : 'required';

        $request->merge([
            'aadhar_number' => preg_replace('/\D+/', '', (string) $request->input('aadhar_number', '')),
        ]);

        $aadharNumberRules = [
            'required',
            'digits:12',
            Rule::unique('trainer_registrations', 'aadhar_number'),
            Rule::unique('users', 'aadhar_number'),
        ];
        if ($isUpdate && $registration) {
            $usersUnique = Rule::unique('users', 'aadhar_number');
            if ($registration->user_id) {
                $usersUnique = $usersUnique->ignore($registration->user_id);
            }

            $aadharNumberRules = [
                'required',
                'digits:12',
                Rule::unique('trainer_registrations', 'aadhar_number')->ignore($registration->id),
                $usersUnique,
            ];
        }

        $validated = $request->validate([
            'instructor_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'email' => $emailRules,
            'instructor_number' => 'required|digits:10',
            'aadhar_number' => $aadharNumberRules,
            'address' => 'required|string|max:1000',
            'state_id' => 'required|exists:states,id',
            'district_id' => [
                'required',
                'integer',
                Rule::exists('districts', 'id')->where(fn ($q) => $q->where('state_id', $request->state_id)),
            ],
            'block' => 'required|string|max:255',
            'martial_art_type' => 'required|string|max:255',
            'blood_group' => 'required|string|max:20',
            'reference_by' => 'required|integer|exists:cordinators,id',
            'comment' => 'nullable|string|max:2000',
            'terms_accepted' => 'accepted',
            'aadhar_doc' => [$aadharRule, 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'qualification_doc' => [$qualificationRule, 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'martial_art_doc' => [$martialRule, 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'photo' => [$photoRule, 'file', 'mimes:jpg,jpeg,png', 'max:3072'],
        ]);

        $district = District::findOrFail($validated['district_id']);
        $validated['district'] = $district->district;

        $blockExists = Block::where('district_id', $district->id)
            ->where('block', $validated['block'])
            ->exists();
        if (!$blockExists) {
            throw ValidationException::withMessages([
                'block' => ['Selected block is invalid for the chosen district.'],
            ]);
        }

        $cordinator = Cordinator::find($validated['reference_by']);
        if (!$cordinator || (int) $cordinator->state_id !== (int) $validated['state_id']) {
            throw ValidationException::withMessages([
                'reference_by' => ['Selected coordinator is invalid for the chosen state.'],
            ]);
        }

        return $validated;
    }

    private function registrationPayload(array $validated): array
    {
        $cordinator = Cordinator::find($validated['reference_by'] ?? null);

        return [
            'instructor_name' => $validated['instructor_name'],
            'father_name' => $validated['father_name'],
            'email' => $validated['email'],
            'instructor_number' => $validated['instructor_number'],
            'aadhar_number' => $validated['aadhar_number'],
            'address' => $validated['address'],
            'state_id' => $validated['state_id'],
            'district' => $validated['district'],
            'block' => $validated['block'],
            'martial_art_type' => $validated['martial_art_type'],
            'blood_group' => $validated['blood_group'],
            'reference_by' => $cordinator?->cordinator_name ?? (string) ($validated['reference_by'] ?? ''),
            'reference_cordinator_id' => $cordinator?->id,
            'comment' => $validated['comment'] ?? null,
        ];
    }

    private function storeDocuments(Request $request, ?TrainerRegistration $existing = null, ?string $instructorCode = null): array
    {
        $paths = [];
        $code = $instructorCode ?: ($existing->instructor_code ?? null);
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

                $paths[$field] = $file->storeAs('trainer_data', $name, 'public');
                continue;
            }

            if (!empty($draft[$field]['path']) && Storage::disk('public')->exists($draft[$field]['path'])) {
                $ext = pathinfo($draft[$field]['path'], PATHINFO_EXTENSION);
                $name = $safeCode.'_'.$suffix.'.'.$ext;
                $final = 'trainer_data/'.$name;

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
            $path = $file->storeAs('trainer_data/tmp', $tmpName, 'public');

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

    private function generateInstructorCode(int $stateId): string
    {
        $state = State::findOrFail($stateId);
        $stateCode = strtoupper($state->code);
        $prefix = 'SOPL_'.$stateCode.'_';
        $start = 258;

        // Number is global across all states: SOPL_HP_258 => next HR gets SOPL_HR_259
        $pattern = '/^SOPL_[A-Z0-9]+_(\d+)$/i';

        $max = $start - 1;

        $userCodes = User::where('instructor_code', 'like', 'SOPL_%')->pluck('instructor_code');
        $regCodes = TrainerRegistration::where('instructor_code', 'like', 'SOPL_%')->pluck('instructor_code');

        foreach ($userCodes->merge($regCodes) as $code) {
            if (preg_match($pattern, (string) $code, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        $next = max($max + 1, $start);

        for ($i = 0; $i < 100; $i++) {
            $candidate = $prefix.$next;
            $exists = User::where('instructor_code', $candidate)->exists()
                || TrainerRegistration::where('instructor_code', $candidate)->exists();
            if (!$exists) {
                return $candidate;
            }
            $next++;
        }

        throw ValidationException::withMessages([
            'state_id' => ['Unable to generate instructor code. Please try again.'],
        ]);
    }

    private function generatePassword(): string
    {
        return 'SOPL@1634';
    }
}
