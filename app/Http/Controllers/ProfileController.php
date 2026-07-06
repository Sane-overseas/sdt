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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Models\AsignedSchool;

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
            foreach($request->school_name as $school){     
                $trainer_school = new AsignedSchool();
                $trainer_school->user_id = $request->input('id');
                $trainer_school->district = $request->input('district');
                $trainer_school->block = $request->input('block');
                $trainer_school->school_name = $school;
                $trainer_school->asigned_by = Auth::user()->id;
                $trainer_school->start_route_plan = null;
                $trainer_school->end_route_plan = null;
                $trainer_school->save();
            }

            foreach($request->school_name as $school){    
                $t_school = School::find($school);
                $t_school->asigned_school = 1;
                $t_school->update();
            }
        }
           
        return Response::json( $trainer);
    }
}
