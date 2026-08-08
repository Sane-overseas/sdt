<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\User;
use App\Models\District;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Services\SchoolAssignmentService;
use App\Services\StateService;
use App\Services\IdCardGenerator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    private array $documentFields = [
        'aadhar_doc' => 'aadhar',
        'qualification_doc' => 'qualification',
        'martial_art_doc' => 'martial_art',
        'photo' => 'photo',
    ];

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function trainerStore(Request $request)
    {
        $request->merge([
            'aadhar_number' => preg_replace('/\D+/', '', (string) $request->input('aadhar_number', '')),
            'number' => preg_replace('/\D+/', '', (string) $request->input('number', '')),
        ]);

        $request->validate([
            'trainer_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => 'required|string|min:6',
            'code' => ['required', 'string', 'max:255', Rule::unique('users', 'instructor_code')],
            'number' => 'required|digits:10',
            'aadhar_number' => [
                'required',
                'digits:12',
                Rule::unique('users', 'aadhar_number'),
                Rule::unique('trainer_registrations', 'aadhar_number'),
            ],
            'address' => 'required|string|max:1000',
            'blood_group' => 'required|string|max:20',
            'martial_art_type' => 'required|string|max:255',
            'cordinator' => 'required|integer',
            'district_name' => 'required|string|max:255',
            'block' => 'required|string|max:255',
            'amount' => 'nullable|numeric',
            'extra_amount' => 'nullable|numeric',
            'aadhar_doc' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'qualification_doc' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'martial_art_doc' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:3072'],
        ]);

        StateService::assertCordinatorInScope((int) $request->cordinator);

        $district = District::where('district', $request->district_name)->first();
        if ($district) {
            StateService::assertDistrictInScope((int) $district->id);
        }

        $stateId = StateService::scopeStateId();
        $docPaths = $this->storeTrainerDocuments($request, $request->code);

        $trainer = User::create([
            'instructor_name' => $request->trainer_name,
            'father_name' => $request->father_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'instructor_code' => $request->code,
            'instructor_number' => $request->number,
            'aadhar_number' => $request->aadhar_number,
            'address' => $request->address,
            'blood_group' => $request->blood_group,
            'martial_art_type' => $request->martial_art_type,
            'cordinator_id' => $request->cordinator,
            'amount' => $request->amount,
            'district' => $request->district_name,
            'block' => $request->input('block'),
            'extra_amount' => $request->extra_amount,
            'state_id' => $stateId,
            'role' => 0,
            'aadhar_doc' => $docPaths['aadhar_doc'] ?? null,
            'qualification_doc' => $docPaths['qualification_doc'] ?? null,
            'martial_art_doc' => $docPaths['martial_art_doc'] ?? null,
            'photo' => $docPaths['photo'] ?? null,
        ]);

        return Response::json($trainer);
    }

    public function updateData(Request $request)
    {
        $request->merge([
            'aadhar_number' => preg_replace('/\D+/', '', (string) $request->input('aadhar_number', '')),
            'number' => preg_replace('/\D+/', '', (string) $request->input('number', '')),
        ]);

        $trainer = User::findOrFail($request->id);
        $auth = Auth::user();

        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ((int) $auth->role === 2) {
            if ((int) ($auth->school_assigned_status ?? 0) !== 1) {
                return response()->json(['message' => 'You do not have permission to edit trainers.'], 403);
            }
            if ((int) $trainer->cordinator_id !== (int) $auth->cordinator_id) {
                return response()->json(['message' => 'You can only edit your own trainers.'], 403);
            }
            // Coordinator cannot reassign trainer to another coordinator or change pay amounts
            $request->merge([
                'cordinator' => $auth->cordinator_id,
                'amount' => $trainer->amount,
                'extra_amount' => $trainer->extra_amount,
            ]);
        } elseif ((int) $auth->role !== 1) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'trainer_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($trainer->id)],
            'code' => ['required', 'string', 'max:255', Rule::unique('users', 'instructor_code')->ignore($trainer->id)],
            'number' => 'required|digits:10',
            'aadhar_number' => [
                'required',
                'digits:12',
                Rule::unique('users', 'aadhar_number')->ignore($trainer->id),
                Rule::unique('trainer_registrations', 'aadhar_number')->ignore($trainer->id, 'user_id'),
            ],
            'address' => 'required|string|max:1000',
            'blood_group' => 'required|string|max:20',
            'martial_art_type' => 'required|string|max:255',
            'cordinator' => 'required|integer',
            'district_name' => 'required|string|max:255',
            'block' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric',
            'extra_amount' => 'nullable|numeric',
            'aadhar_doc' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'qualification_doc' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'martial_art_doc' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:3072'],
        ]);

        StateService::assertCordinatorInScope((int) $request->cordinator);

        $district = District::where('district', $request->district_name)->first();
        if ($district) {
            StateService::assertDistrictInScope((int) $district->id);
        }

        $docPaths = $this->storeTrainerDocuments($request, $request->code, $trainer);

        $trainer->instructor_name = $request->trainer_name;
        $trainer->father_name = $request->father_name;
        $trainer->email = $request->email;
        $trainer->instructor_code = $request->code;
        $trainer->instructor_number = $request->number;
        $trainer->aadhar_number = $request->aadhar_number;
        $trainer->address = $request->address;
        $trainer->blood_group = $request->blood_group;
        $trainer->martial_art_type = $request->martial_art_type;
        $trainer->cordinator_id = $request->cordinator;
        $trainer->amount = $request->amount;
        $trainer->district = $request->district_name;
        $trainer->extra_amount = $request->extra_amount;

        if ($request->filled('block')) {
            $trainer->block = $request->input('block');
        }

        foreach ($this->documentFields as $field => $suffix) {
            if (!empty($docPaths[$field])) {
                $trainer->{$field} = $docPaths[$field];
            }
        }

        $trainer->save();

        $schoolIds = $request->input('school_name');
        if (!empty($schoolIds)) {
            if (!is_array($schoolIds)) {
                $schoolIds = [$schoolIds];
            }

            $districtId = $request->input('district');
            if (empty($districtId) && $district) {
                $districtId = $district->id;
            }

            $block = $request->input('block');
            if (empty($block)) {
                return response()->json(['message' => 'Please select Block before assigning schools.'], 422);
            }
            if (empty($districtId)) {
                return response()->json(['message' => 'Please select District before assigning schools.'], 422);
            }

            $result = SchoolAssignmentService::assignSchools(
                $schoolIds,
                (int) $request->input('id'),
                $districtId,
                $block,
                Auth::user()->id
            );

            if (isset($result['error'])) {
                return response()->json(['message' => $result['error']], 422);
            }
        }

        return Response::json($trainer);
    }

    private function storeTrainerDocuments(Request $request, string $instructorCode, ?User $existing = null): array
    {
        $paths = [];
        $safeCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $instructorCode);

        foreach ($this->documentFields as $field => $suffix) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $ext = strtolower($file->getClientOriginalExtension());
            $name = $safeCode.'_'.$suffix.'.'.$ext;

            if ($existing && $existing->{$field}) {
                Storage::disk('public')->delete($existing->{$field});
            }

            $paths[$field] = $file->storeAs('trainer_data', $name, 'public');
        }

        return $paths;
    }

    public function myIdCard(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !in_array((int) $user->role, [0, 2], true)) {
            abort(403, 'ID card is only available for trainers and coordinators.');
        }

        try {
            $path = $this->resolveIdCardPath($user);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->with('error', 'Could not generate ID card. Please contact admin.');
        }

        return view('trainer.id-card', [
            'user' => $user,
            'idCardUrl' => asset('storage/'.$path).'?v='.time(),
            'downloadUrl' => route('my-id-card.download'),
        ]);
    }

    public function downloadIdCard(): BinaryFileResponse|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !in_array((int) $user->role, [0, 2], true)) {
            abort(403, 'ID card is only available for trainers and coordinators.');
        }

        try {
            $path = $this->resolveIdCardPath($user);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('my-id-card')->with('error', 'Could not generate ID card. Please contact admin.');
        }

        $absolute = Storage::disk('public')->path($path);
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $user->instructor_code).'_ID_Card.png';

        return response()->download($absolute, $filename);
    }

    private function resolveIdCardPath(User $user): string
    {
        $safeCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper((string) $user->instructor_code));
        $relative = 'id_cards/'.$safeCode.'.png';

        if (!Storage::disk('public')->exists($relative)) {
            $relative = app(IdCardGenerator::class)->generate([
                'name' => $user->instructor_name,
                'code' => $user->instructor_code,
                'blood_group' => $user->blood_group,
                'designation' => (int) $user->role === 2 ? 'COORDINATOR' : 'TRAINER',
                'photo_path' => $user->photo,
            ]);
        }

        return $relative;
    }
}
