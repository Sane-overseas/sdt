<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\School;
use App\Models\District;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Models\AsignedSchool;
use App\Services\SchoolAssignmentService;
use App\Services\StateService;

class ProfileController extends Controller
{
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
        $request->validate([
            'trainer_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'code' => 'required',
            'number' => 'required',
            'cordinator' => 'required',
            'district_name' => 'required',
        ]);

        StateService::assertCordinatorInScope((int) $request->cordinator);

        $district = District::where('district', $request->district_name)->first();
        if ($district) {
            StateService::assertDistrictInScope((int) $district->id);
        }

        $stateId = StateService::scopeStateId();

        $trainer = User::create([
            'instructor_name' => $request->trainer_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'instructor_code' => $request->code,
            'instructor_number' => $request->number,
            'cordinator_id' => $request->cordinator,
            'amount' => $request->amount,
            'district' => $request->district_name,
            'extra_amount' => $request->extra_amount,
            'state_id' => $stateId,
            'role' => 0,
        ]);

        return Response::json( $trainer);
    }

    public function updateData(Request $request){
        
        $request->validate([
            'trainer_name' => 'required',
            'email' => 'required|email', 
            'code' => 'required',
            'number' => 'required',
            'cordinator' => 'required',
            'district_name' => 'required',
        ]);

        StateService::assertCordinatorInScope((int) $request->cordinator);

        $district = District::where('district', $request->district_name)->first();
        if ($district) {
            StateService::assertDistrictInScope((int) $district->id);
        }

        $trainer = User::find($request->id);
        $trainer->instructor_name = $request->trainer_name ;
        $trainer->email = $request->email ;
        $trainer->instructor_code = $request->code ;
        $trainer->instructor_number = $request->number;
        $trainer->cordinator_id = $request->cordinator ;
        $trainer->amount = $request->amount;
        $trainer->district = $request->district_name;
        $trainer->extra_amount = $request->extra_amount;
        $trainer->update();

        if($request->school_name !== null){
            $result = SchoolAssignmentService::assignSchools(
                $request->school_name,
                (int) $request->input('id'),
                $request->input('district'),
                $request->input('block'),
                Auth::user()->id
            );

            if (isset($result['error'])) {
                return response()->json(['message' => $result['error']], 422);
            }
        }
           
        return Response::json( $trainer);
    }
}
