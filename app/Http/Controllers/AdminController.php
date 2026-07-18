<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use App\Models\Image;
use App\Models\Completion;
use App\Models\District;
use App\Models\School;
use App\Models\Block;
use App\Models\User;
use App\Models\Cordinator;
use App\Models\Distribution;
use App\Models\AsignedSchool;
use App\Models\PaidSchool;
use App\Models\AdvancePayment;
use App\Models\Holiday;
use App\Models\AcademicSession;
use App\Models\State;
use App\Models\Testimonial;
use App\Services\AcademicSessionService;
use App\Services\HolidayService;
use App\Services\SchoolAssignmentService;
use App\Services\SessionUploadService;
use App\Services\StateService;
use View;
use Session;
use Config;
use DB;
use Illuminate\Validation\Rule;
use DateTime;
use Carbon\Carbon;

class AdminController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private function reportSessionId(): ?int
    {
        return AcademicSessionService::scopeSessionId();
    }

    private function reportDistrictIds(): array
    {
        return StateService::districtIds();
    }

    //adding and updating the trainer detail and calculating the total amount as received the amount and extra amount
    public function addTrainers()
    {
        $stateId = StateService::scopeStateId();
        $trainers = User::where('role', '!=', 1)
            ->when($stateId, fn ($query) => $query->where('state_id', $stateId))
            ->get();

        foreach ($trainers as $trainer) {
            $trainer->total_amount = $trainer->amount + $trainer->extra_amount;
            $trainer->save();
        }

        $cordinator = StateService::cordinatorsQuery()->orderBy('cordinator_name')->get();
        $district = StateService::districtsQuery()->orderBy('district')->get()->toArray();

        return view('admin.trainers')
            ->with('trainers', $trainers)
            ->with('district', $district)
            ->with('cordinator', $cordinator);
    }
    public function OnGoingTrainers()
    {
        $districtIds = $this->reportDistrictIds();
        $a_schools = AsignedSchool::whereIn('district', $districtIds)->get()->toArray();
        $schools = StateService::schoolsQuery()->get();
        $trainers = StateService::trainersQuery()->get();
        $district = StateService::districtsQuery()->get()->toArray();
        $distribution = Distribution::get()->toArray();
        $cordinator  = StateService::cordinatorsQuery()->get()->toArray();
        $trainersWithAssigned = StateService::trainersQuery()->with('asigned_schools')->get()->toArray();
        return view('TrainersReporting.ongoing-trainers')
            ->with('schools', $schools)
            ->with('trainers', $trainers)
            ->with('a_schools', $a_schools)
            ->with('trainersWithAssigned', $trainersWithAssigned)
            ->with('district', $district)
            ->with('cordinator', $cordinator);
    }
    public function NotWorkingTrainers()
    {
        $districtIds = $this->reportDistrictIds();
        $a_schools = AsignedSchool::whereIn('district', $districtIds)->get()->toArray();
        $schools = StateService::schoolsQuery()->get();
        $trainers = StateService::trainersQuery()->get();
        $district = StateService::districtsQuery()->get()->toArray();
        $distribution = Distribution::get()->toArray();
        $cordinator  = StateService::cordinatorsQuery()->get()->toArray();
        $trainersWithAssigned = StateService::trainersQuery()->with('asigned_schools')->get()->toArray();

        return view('TrainersReporting.notworking-trainers')
            ->with('schools', $schools)
            ->with('trainers', $trainers)
            ->with('a_schools', $a_schools)
            ->with('trainersWithAssigned', $trainersWithAssigned)
            ->with('district', $district)
            ->with('cordinator', $cordinator);
    }
    public function TrainersSchoolsData()
    {
        $this->schoolCompleteStatus();
        $districtIds = $this->reportDistrictIds();
        $a_schools = AsignedSchool::whereIn('district', $districtIds)->get()->toArray();
        $schools = StateService::schoolsQuery()->get();
        $trainers = StateService::trainersQuery()->get();
        $district = StateService::districtsQuery()->get()->toArray();
        $distribution = Distribution::get()->toArray();
        $cordinator  = StateService::cordinatorsQuery()->get()->toArray();
        $trainersWithAssigned = StateService::trainersQuery()->with('asigned_schools')->get()->toArray();

        return view('TrainersReporting.trainers-schools-data')
            ->with('schools', $schools)
            ->with('trainers', $trainers)
            ->with('a_schools', $a_schools)
            ->with('trainersWithAssigned', $trainersWithAssigned)
            ->with('district', $district)
            ->with('cordinator', $cordinator);
    }

    public function Cordinators()
    {
        $trainers = StateService::trainersQuery()->get();
        $cordinator = StateService::coordinatorUsersQuery()->get();
        $new_cordinator = StateService::cordinatorsQuery()->orderBy('cordinator_name')->get()->toArray();
        $district = StateService::districtsQuery()->orderBy('district')->get()->toArray();

        return view('admin.cordinator')
            ->with('district', $district)
            ->with('trainers', $trainers)
            ->with('new_cordinator', $new_cordinator)
            ->with('cordinator', $cordinator);
    }

    public function ClaimTraniers()
    {
        $sessionId = $this->reportSessionId();
        $districtIds = $this->reportDistrictIds();
        $a_schools = AsignedSchool::whereIn('district', $districtIds)->get()->toArray();
        $schools = StateService::schoolsQuery()->get();
        $trainers = User::where('role', '!=', 1)
            ->where('state_id', StateService::scopeStateId())
            ->with('asigned_schools')
            ->get()->toArray();
        $district = StateService::districtsQuery()->get()->toArray();
        $distribution = Distribution::get()->toArray();
        $cordinator  = StateService::cordinatorsQuery()->get()->toArray();
        $trainersWithAssigned = StateService::trainersQuery()->with('asigned_schools')->get()->toArray();
        $total_amount = User::sum('total_amount');

        $sessionPaidCounts = PaidSchool::withoutGlobalScopes()
            ->where('session_id', $sessionId)
            ->select('user_id', DB::raw('COUNT(DISTINCT school_id) as paid_count'))
            ->groupBy('user_id')
            ->pluck('paid_count', 'user_id')
            ->toArray();

        $sessionAdvanceTotals = AdvancePayment::withoutGlobalScopes()
            ->where('session_id', $sessionId)
            ->select('user_id', DB::raw('SUM(payment) as total_payment'))
            ->groupBy('user_id')
            ->pluck('total_payment', 'user_id')
            ->toArray();

        return view('TrainersReporting.claim-traniers')
            ->with('schools', $schools)
            ->with('trainers', $trainers)
            ->with('a_schools', $a_schools)
            ->with('trainersWithAssigned', $trainersWithAssigned)
            ->with('district', $district)
            ->with('total_amount', $total_amount)
            ->with('cordinator', $cordinator)
            ->with('sessionPaidCounts', $sessionPaidCounts)
            ->with('sessionAdvanceTotals', $sessionAdvanceTotals);
    }

    public function paidSchools()
    {
        $sessionId = $this->reportSessionId();
        $schools = StateService::schoolsQuery()->get()->toArray();
        $trainers = User::where('state_id', StateService::scopeStateId())->get()->toArray();
        $district = StateService::districtsQuery()->get()->toArray();
        $districtIds = $this->reportDistrictIds();

        $paidSchools = DB::table('paid_schools')
            ->join('asigned_schools', 'asigned_schools.school_name', '=', 'paid_schools.school_id')
            ->join('schools', 'schools.id', '=', 'asigned_schools.school_name')
            ->where('paid_schools.session_id', $sessionId)
            ->where('asigned_schools.session_id', $sessionId)
            ->whereIn('schools.district_id', $districtIds ?: [0])
            ->select('paid_schools.*', 'asigned_schools.*')
            ->get()->toArray();

        return view('SchoolsReporting.paid-schools')
            ->with('paidSchools', $paidSchools)
            ->with('schools', $schools)
            ->with('district', $district)
            ->with('trainers', $trainers);
    }

    public function unPaidSchools()
    {
        $sessionId = $this->reportSessionId();
        $schools = StateService::schoolsQuery()->get()->toArray();
        $trainers = User::where('state_id', StateService::scopeStateId())->get()->toArray();
        $district = StateService::districtsQuery()->get()->toArray();
        $districtIds = $this->reportDistrictIds();

        $unpaid_schools = DB::table('asigned_schools')
            ->join('users', 'users.id', '=', 'asigned_schools.user_id')
            ->join('schools', 'schools.id', '=', 'asigned_schools.school_name')
            ->where('asigned_schools.session_id', $sessionId)
            ->whereIn('schools.district_id', $districtIds ?: [0])
            ->where('users.claim_note', '!=', null)
            ->where('asigned_schools.paid_status', '=', 0)
            ->where('asigned_schools.route_date', '!=', null)
            ->where('asigned_schools.status', '=', 1)
            ->select('users.*', 'asigned_schools.*')
            ->get()->toArray();

        return view('SchoolsReporting.unpaid-schools')
            ->with('unpaid_schools', $unpaid_schools)
            ->with('schools', $schools)
            ->with('district', $district)
            ->with('trainers', $trainers);
    }

    public function todayAssignedSchools(Request $request)
    {
        $districtIds = $this->reportDistrictIds();
        $schools = StateService::schoolsQuery()->get()->toArray();
        $trainers = User::where('state_id', StateService::scopeStateId())->get()->toArray();
        $district = StateService::districtsQuery()->get()->toArray();

        if ($request->custom_date == null) {
            $a_schools = AsignedSchool::whereIn('district', $districtIds ?: [0])->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->get()->toArray();
        } else {
            $a_schools = AsignedSchool::whereIn('district', $districtIds ?: [0])->whereDate('created_at', date('Y-m-d', strtotime($request->custom_date)))->orderBy('created_at', 'DESC')->get()->toArray();
        }

        return view('SchoolsReporting.assigned-schools')
            ->with('a_schools', $a_schools)
            ->with('schools', $schools)
            ->with('district', $district)
            ->with('trainers', $trainers);
    }

    public function routePlanSchools(Request $request)
    {
        $districtIds = $this->reportDistrictIds();
        $schools = StateService::schoolsQuery()->get()->toArray();
        $trainers = User::where('state_id', StateService::scopeStateId())->get()->toArray();
        $district = StateService::districtsQuery()->get()->toArray();

        if ($request->custom_date == null) {
            $addRoutePlan = AsignedSchool::whereIn('district', $districtIds ?: [0])->whereDate('add_route_plan_date', date('Y-m-d'))
                ->orderBy('add_route_plan_date', 'DESC')->get()->toArray();
        } else {
            $addRoutePlan = AsignedSchool::whereIn('district', $districtIds ?: [0])->whereDate('add_route_plan_date', date('Y-m-d', strtotime($request->custom_date)))
                ->orderBy('add_route_plan_date', 'DESC')->get()->toArray();
        }

        return view('SchoolsReporting.route-plan-schools')
            ->with('addRoutePlan', $addRoutePlan)
            ->with('schools', $schools)
            ->with('district', $district)
            ->with('trainers', $trainers);
    }

    public function cordinatorStore(Request $request)
    {
        $stateId = StateService::scopeStateId();
        if (!$stateId) {
            return response()->json(['message' => 'Please select a state first.'], 422);
        }

        $request->validate([
            'cordinator_name' => 'required',
            'code' => [
                'required',
                Rule::unique('cordinators', 'cordinator_code')->where(fn ($query) => $query->where('state_id', $stateId)),
            ],
        ]);

        $cordinator = new Cordinator;
        $cordinator->cordinator_name = $request->cordinator_name;
        $cordinator->cordinator_code = $request->code;
        $cordinator->state_id = $stateId;
        $cordinator->save();

        return Response::json($cordinator);
    }

    public function cordinatorData($id)
    {
        $user = User::where('id', $id)->first();
        $trainers = User::where('cordinator_id', $user['cordinator_id'])
            ->where('role', 0)
            ->when($user->state_id, fn ($query) => $query->where('state_id', $user->state_id))
            ->get();

        foreach ($trainers as $trainer) {
            $trainer->setRelation(
                'asigned_schools',
                AsignedSchool::withoutGlobalScopes()->where('user_id', $trainer->id)->get()
            );
        }

        return view('admin.cdr-trainers')
            ->with('trainers', $trainers);
    }

    public function trainersLogs(Request $request)
    {
        $sessionId = $this->reportSessionId();
        $district = StateService::districtsQuery()->get()->toArray();
        $schools = StateService::schoolsQuery()->get();
        $districtIds = $this->reportDistrictIds();
        $today_logs = DB::table('asigned_schools')
            ->join('users', 'users.id', '=', 'asigned_schools.user_id')
            ->join('schools', 'schools.id', '=', 'asigned_schools.school_name')
            ->where('asigned_schools.session_id', $sessionId)
            ->whereIn('schools.district_id', $districtIds ?: [0])
            ->where('asigned_schools.end_date', '=', Carbon::now()->format('d/m/Y'))
            ->select('users.*', 'asigned_schools.*')
            ->get()->toArray();

        $previous_logs = DB::table('asigned_schools')
            ->join('users', 'users.id', '=', 'asigned_schools.user_id')
            ->join('schools', 'schools.id', '=', 'asigned_schools.school_name')
            ->where('asigned_schools.session_id', $sessionId)
            ->whereIn('schools.district_id', $districtIds ?: [0])
            ->where('asigned_schools.end_date', '>=', Carbon::yesterday()->format('d/m/y'))
            ->where('asigned_schools.uc_submitted', '=', 0)
            ->select('users.*', 'asigned_schools.*')
            ->get()->toArray();

        $tommarow_logs = DB::table('asigned_schools')
            ->join('users', 'users.id', '=', 'asigned_schools.user_id')
            ->join('schools', 'schools.id', '=', 'asigned_schools.school_name')
            ->where('asigned_schools.session_id', $sessionId)
            ->whereIn('schools.district_id', $districtIds ?: [0])
            ->where('asigned_schools.end_date', '=', Carbon::tomorrow()->format('d/m/Y'))
            ->select('users.*', 'asigned_schools.*')
            ->get()->toArray();

        $custom_date = (explode("-", $request->route_date));
        $custom_log = [];
        if ($custom_date[0] != '') {
            $start = trim($custom_date[0]);
            $end = trim($custom_date[1]);
            $custom_log = DB::table('asigned_schools')
                ->join('users', 'users.id', '=', 'asigned_schools.user_id')
                ->join('schools', 'schools.id', '=', 'asigned_schools.school_name')
                ->where('asigned_schools.session_id', $sessionId)
                ->whereIn('schools.district_id', $districtIds ?: [0])
                ->where('asigned_schools.end_date', '>=', $start)
                ->where('asigned_schools.end_date', '<=', $end)
                ->get()->toArray();
        }

        return view('admin.logs')
            ->with('today_logs', $today_logs)
            ->with('previous_logs', $previous_logs)
            ->with('tommarow_logs', $tommarow_logs)
            ->with('custom_log', $custom_log)
            ->with('district', $district)
            ->with('schools', $schools);
    }

    public function salaryStatus(Request $request)
    {
        if (!AcademicSessionService::isWritableContext()) {
            return redirect()->back()->with('error', 'Payments are locked for closed/archive sessions. Switch to active session.');
        }

        $sessionId = $this->reportSessionId();

        if ($request->paid_status != null) {
            $salary_status = User::findOrFail($request->id);
            $total_salary = $salary_status->amount * count($request->paid_status);

            foreach ($request->paid_status as $key => $school) {
                $paid_schools = new PaidSchool;
                $paid_schools->user_id = $request->id;
                $paid_schools->school_id = $school;
                $paid_schools->paid_by = Auth::user()->id;
                $paid_schools->paid_date = date('d-m-Y', strtotime($request->paid_date));
                $paid_schools->session_id = $sessionId;
                $paid_schools->save();

                AsignedSchool::withoutGlobalScopes()
                    ->where('session_id', $sessionId)
                    ->where('user_id', $request->id)
                    ->where('school_name', $school)
                    ->update(['paid_status' => 1]);
            }

            if ($request->extra_amount != null && (float) $request->extra_amount > 0) {
                $advancePayment = new AdvancePayment;
                $advancePayment->user_id = $request->id;
                $advancePayment->role = 'Trainer';
                $advancePayment->payment = $request->extra_amount;
                $advancePayment->payment_date = date('d-m-Y', strtotime($request->paid_date));
                $advancePayment->session_id = $sessionId;
                $advancePayment->save();
            }
        } else {
            if ($request->extra_amount != null && (float) $request->extra_amount > 0) {
                $advancePayment = new AdvancePayment;
                $advancePayment->user_id = $request->id;
                $advancePayment->role = 'Trainer';
                $advancePayment->payment = $request->extra_amount;
                $advancePayment->payment_date = date('d-m-Y', strtotime($request->paid_date));
                $advancePayment->session_id = $sessionId;
                $advancePayment->save();
            }
        }

        return redirect()->back();
    }

    public function trainerDetail($id)
    {
        $trainer = User::where('id', $id)
            ->with('videos', 'images', 'completions', 'distributions', 'asigned_schools')
            ->first();

        if (!$trainer) {
            abort(404, 'Trainer not found.');
        }

        $stateId = StateService::scopeStateId();
        if ($stateId && (int) $trainer->state_id !== (int) $stateId) {
            return redirect()
                ->route('add_trainers')
                ->with('error', 'That trainer belongs to another state. Showing trainers for the selected state.');
        }

        $trainer_data = $trainer->toArray();
        $user = User::get()->toArray();
        $district = StateService::districtsQuery()->orderBy('district')->get();
        $school = StateService::schoolsQuery()->orderBy('school_name')->get();
        $cordinator = StateService::cordinatorsQuery()->orderBy('cordinator_name')->get();

        return view('admin.trainer-data')
            ->with('user', $user)
            ->with('trainer_data', $trainer_data)
            ->with('school', $school)
            ->with('cordinator', $cordinator)
            ->with('district', $district);
    }

    public function asignedSchoolDelete($id, $sid)
    {

        $assignment = AsignedSchool::withoutGlobalScopes()->find($id);
        if (!$assignment) {
            return response()->json(['error' => 'Assignment not found.'], 404);
        }

        $school = School::where('id', $sid)->first()->toArray();
        $sessionId = $assignment->session_id;

        $video = Video::withoutGlobalScopes()
            ->where('school_name', $school['school_name'])
            ->where('session_id', $sessionId)
            ->first();
        $image = Image::withoutGlobalScopes()
            ->where('school_name', $school['school_name'])
            ->where('session_id', $sessionId)
            ->first();
        $uc = Completion::withoutGlobalScopes()
            ->where('school_name', $school['school_name'])
            ->where('session_id', $sessionId)
            ->first();
        $dc = Distribution::withoutGlobalScopes()
            ->where('school_name', $school['school_name'])
            ->where('session_id', $sessionId)
            ->first();

        if ($video == null && $image == null && $uc == null && $dc == null) {
            $schoolId = (int) $sid;
            $assignment->delete();

            SchoolAssignmentService::syncSchoolAssignedFlag($schoolId);

            return response()->json([
                'success' => 'Record deleted successfully!'
            ]);
        } else {
            return redirect()->back()->with('error', 'Some data add in this School Please Check!');
        }
    }

    public function videoStatus(Request $request)
    {
        $vid = Video::where('id', $request->video_id)->first()->toArray();

        if ($vid != null) {
            $video = Video::findOrFail($request->video_id);
            if ((int) $request->video_status === 1) {
                if ($blocked = $this->approveBlockedIfRejected($video, 'video_note', 'video')) {
                    return $blocked;
                }
            }
            $video->status = $request->video_status;
            $video->save();

            if ((int) $vid['session_id'] === (int) AcademicSessionService::activeId()) {
                $school_image = School::findOrFail($vid['school_id']);
                $school_image->video_status = $request->video_status;
                $school_image->save();
            }
            SessionUploadService::syncAssignmentStatuses($vid['session_id']);
            return response()->json(['success' => 'User status updated successfully.']);
        } else {
            return redirect()->back()->with('error', 'Somethig Wrong Please Check!');
        }
    }

    public function fstvideoDetail($id)
    {
        $video = Video::findOrFail($id);
        if ($blocked = $this->approvedDeleteBlocked($video, 'video')) {
            return $blocked;
        }
        $this->deletePublicStorageFile('videos/'.$video->fst_video);
        $video->fst_video = null;
        $video->status = 0;
        $video->save();
        return response()->json($video);
    }

    public function sndvideoDetail($id)
    {
        $video = Video::findOrFail($id);
        if ($blocked = $this->approvedDeleteBlocked($video, 'video')) {
            return $blocked;
        }
        $this->deletePublicStorageFile('videos/'.$video->snd_video);
        $video->snd_video = null;
        $video->status = 0;
        $video->save();
        return response()->json($video);
    }

    public function deleteVideos($id, $sid)
    {
        $video = Video::findOrFail($id);
        if ($blocked = $this->approvedDeleteBlocked($video, 'video')) {
            return $blocked;
        }
        $this->deletePublicStorageFile('videos/'.$video->fst_video);
        $this->deletePublicStorageFile('videos/'.$video->snd_video);
        $video->delete();

        $videoStatus = School::findOrFail($sid);
        $videoStatus->video_status = 0;
        $videoStatus->save();

        return response()->json(['success' => true]);
    }


    public function imageStatus(Request $request)
    {
        $img = Image::where('id', $request->image_id)->first()->toArray();

        if ($img != null) {
            $image = Image::findOrFail($request->image_id);
            if ((int) $request->image_status === 1) {
                if ($blocked = $this->approveBlockedIfRejected($image, 'image_note', 'image')) {
                    return $blocked;
                }
            }
            $image->status = $request->image_status;
            $image->save();

            if ((int) $img['session_id'] === (int) AcademicSessionService::activeId()) {
                $school_image = School::findOrFail($img['school_id']);
                $school_image->image_status = $request->image_status;
                $school_image->save();
            }
            SessionUploadService::syncAssignmentStatuses($img['session_id']);
            return response()->json(['success' => 'User status updated successfully.']);
        } else {
            return redirect()->back()->with('error', 'Somethig Wrong Please Check!');
        }
    }

    public function imagesDetail($id, $imgid)
    {
        $image = Image::findOrFail($id);
        if ($blocked = $this->approvedDeleteBlocked($image, 'image')) {
            return $blocked;
        }

        $fields = [
            1 => 'ifsb_image',
            2 => 'group_image',
            3 => 'fst_aimage',  
            4 => 'snd_aimage',
            5 => 'trd_aimage',
        ];

        if (!isset($fields[$imgid])) {
            return response()->json(['error' => 'Invalid image type.'], 422);
        }

        $field = $fields[$imgid];
        $this->deletePublicStorageFile('images/'.$image->{$field});
        $image->{$field} = null;
        $image->status = 0;
        $image->save();

        return response()->json($image);
    }

    public function deleteImages($id, $sid)
    {
        $image = Image::findOrFail($id);
        if ($blocked = $this->approvedDeleteBlocked($image, 'image')) {
            return $blocked;
        }

        foreach (['ifsb_image', 'group_image', 'fst_aimage', 'snd_aimage', 'trd_aimage'] as $field) {
            $this->deletePublicStorageFile('images/'.$image->{$field});
        }
        $image->delete();

        $imageStatus = School::findOrFail($sid);
        $imageStatus->image_status = 0;
        $imageStatus->save();

        return response()->json(['success' => true]);
    }

    public function completionStatus(Request $request)
    {
        $uc = Completion::where('id', $request->completion_id)->first();
        if ($uc != null) {
            $completion = Completion::findOrFail($request->completion_id);
            if ((int) $request->completion_status === 1) {
                if (!empty($completion->completion_note) && (int) $completion->emergency_approved !== 1) {
                    return response()->json(['error' => 'Rejected completion must be re-uploaded before approval.'], 422);
                }
            }
            $completion->status = $request->completion_status;
            $completion->save();

            if ((int) $uc->session_id === (int) AcademicSessionService::activeId()) {
                $school_completion = School::findOrFail($uc->school_id);
                $school_completion->completion_status = $request->completion_status;
                $school_completion->save();
            }
            SessionUploadService::syncAssignmentStatuses($uc->session_id);
            return response()->json(['success' => 'User status updated successfully.']);
        } else {
            return redirect()->back()->with('error', 'Somethig Wrong Please Check!');
        }
    }

    public function completionDetail($id, $sid)
    {
        $completion = Completion::find($id);
        if (!$completion) {
            return response()->json(['error' => 'Record not found.'], 404);
        }
        if ($blocked = $this->approvedDeleteBlocked($completion, 'completion')) {
            return $blocked;
        }

        $sessionId = $completion->session_id;
        $this->deletePublicStorageFile('completion/'.$completion->completion_file);
        $completion->delete();

        AsignedSchool::withoutGlobalScopes()
            ->where('school_name', $sid)
            ->where('session_id', $sessionId)
            ->update(['uc_submitted' => 0]);

        SessionUploadService::syncAssignmentStatuses($sessionId);

        return response()->json(['success' => true]);
    }

    public function distributionsStatus(Request $request)
    {
        $dc = Distribution::where('id', $request->distributions_id)->first();
        if ($dc != null) {
            $distributions = Distribution::findOrFail($request->distributions_id);
            if ((int) $request->distributions_status === 1) {
                if ($blocked = $this->approveBlockedIfRejected($distributions, 'distribution_note', 'distribution')) {
                    return $blocked;
                }
            }
            $distributions->status = $request->distributions_status;
            $distributions->save();

            if ((int) $dc->session_id === (int) AcademicSessionService::activeId()) {
                $school_distributions = School::findOrFail($dc->school_id);
                $school_distributions->distribution_status = $request->distributions_status;
                $school_distributions->save();
            }
            return response()->json(['success' => 'User status updated successfully.']);
        } else {
            return redirect()->back()->with('error', 'Somethig Wrong Please Check!');
        }
    }

    public function distributionDetail($id)
    {
        $distribution = Distribution::findOrFail($id);
        if ($blocked = $this->approvedDeleteBlocked($distribution, 'distribution')) {
            return $blocked;
        }
        $this->deletePublicStorageFile('distribution/'.$distribution->distribution_file);
        $distribution->delete();

        return response()->json(['success' => true]);
    }

    public function getSchools()
    {
        $this->schoolCompleteStatus();

        $schools = StateService::schoolsQuery()->get();
        $district = StateService::districtsQuery()->get();
        return view('admin.schools')
            ->with('schools', $schools)
            ->with('district', $district);
    }

    public function videoNote(Request $request)
    {
        $request->validate([
            'video_note' => 'required',
        ]);

        $video = Video::findOrFail($request->id);
        $video->video_note = $request->video_note;
        $video->status = 0;
        $video->save();
        return redirect()->back();
    }

    public function testimonialStatus(Request $request)
    {
        $row = Testimonial::where('id', $request->testimonial_id)->first();
        if (!$row) {
            return response()->json(['error' => 'Something Wrong Please Check!'], 404);
        }

        if ((int) $request->testimonial_status === 1) {
            if ($blocked = $this->approveBlockedIfRejected($row, 'testimonial_note', 'testimonial')) {
                return $blocked;
            }
        }

        $row->status = (int) $request->testimonial_status;
        $row->save();

        return response()->json(['success' => 'Testimonial status updated successfully.']);
    }

    public function testimonialNote(Request $request)
    {
        $request->validate([
            'testimonial_note' => 'required',
            'id' => 'required|exists:testimonials,id',
        ]);

        $testimonial = Testimonial::findOrFail($request->id);
        $testimonial->testimonial_note = $request->testimonial_note;
        $testimonial->status = 0;
        $testimonial->save();

        return redirect()->back();
    }

    public function deleteTestimonial($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        if ($blocked = $this->approvedDeleteBlocked($testimonial, 'testimonial')) {
            return $blocked;
        }
        if ($testimonial->testimonial_video) {
            Storage::disk('public')->delete('testimonials/'.$testimonial->testimonial_video);
        }
        $testimonial->delete();

        return response()->json(['success' => true]);
    }

    public function imageNote(Request $request)
    {
        $request->validate([
            'image_note' => 'required',
        ]);

        $video = Image::findOrFail($request->id);
        $video->image_note = $request->image_note;
        $video->status = 0;
        $video->save();
        return redirect()->back();
    }

    public function distributionNote(Request $request)
    {
        $request->validate([
            'distribution_note' => 'required',
        ]);

        $video = Distribution::findOrFail($request->id);
        $video->distribution_note = $request->distribution_note;
        $video->status = 0;
        $video->save();
        return redirect()->back();
    }

    public function completionNote(Request $request)
    {
        $request->validate([
            'completion_note' => 'required',
        ]);

        if ($request->emergency_approved) {
            $completion_note = Completion::findOrFail($request->id);
            $completion_note->completion_note = $request->completion_note;
            $completion_note->status = 1;
            $completion_note->emergency_approved = 1;
            $completion_note->save();
        } else {
            $completion_note = Completion::findOrFail($request->id);
            $completion_note->completion_note = $request->completion_note;
            $completion_note->status = 0;
            $completion_note->emergency_approved = 0;
            $completion_note->save();
        }
        return redirect()->back();
    }

    public function trainerData($id)
    {
        $trainer_data = User::where('id', $id)->with('videos', 'images', 'completions', 'distributions', 'asigned_schools')->first()->toArray();

        $district = StateService::districtsQuery()->get();
        $school = StateService::schoolsQuery()->get();
        $cordinator = StateService::cordinatorsQuery()->get();
        return view('trainerLogs.trainer-details')
            ->with('trainer_data', $trainer_data)
            ->with('school', $school)
            ->with('cordinator', $cordinator)
            ->with('district', $district);
    }

    public function remarkNote(Request $request)
    {
        $remark = AsignedSchool::findOrFail($request->id);
        if ($remark->remark == null) {
            $remark->remark = date("d/m/y") . " - " . $request->remark;
        } else {
            $remark->remark = $remark->remark . " OR " . date("d/m/y") . " - " . $request->remark;
        }

        $remark->save();
        return redirect()->back();
    }

    public function uploadedData(Request $request)
    {
        $schoolIds = StateService::schoolsQuery()->pluck('id')->all();
        $schoolFilter = $schoolIds ?: [0];

        if ($request->custom_date_data != null) {
            $date = $request->custom_date_data;
            $dateFilter = function ($query) use ($date) {
                $query->whereDate('updated_at', $date)->orWhereDate('created_at', $date);
            };
            $videos = Video::orderBy('updated_at', 'DESC')
                ->whereIn('school_id', $schoolFilter)->where($dateFilter)->get()->toArray();
            $images = Image::orderBy('updated_at', 'DESC')
                ->whereIn('school_id', $schoolFilter)->where($dateFilter)->get()->toArray();
            $completion = Completion::orderBy('updated_at', 'DESC')
                ->whereIn('school_id', $schoolFilter)->where($dateFilter)->get()->toArray();
            $distributions = Distribution::orderBy('updated_at', 'DESC')
                ->whereIn('school_id', $schoolFilter)->where($dateFilter)->get()->toArray();
            $testimonials = Testimonial::orderBy('updated_at', 'DESC')
                ->whereIn('school_id', $schoolFilter)->where($dateFilter)->get()->toArray();
        } elseif ($request->route_date != null) {
            $custom_date = (explode("/", $request->route_date));
            $startDate = trim($custom_date[0] ?? '');
            $endDate = trim($custom_date[1] ?? '');
            $rangeFilter = function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(updated_at)'), [$startDate, $endDate])
                    ->orWhereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            };
            $videos = Video::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)
                ->where($rangeFilter)->get()->toArray();
            $images = Image::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)
                ->where($rangeFilter)->get()->toArray();
            $completion = Completion::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)
                ->where($rangeFilter)->get()->toArray();
            $distributions = Distribution::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)
                ->where($rangeFilter)->get()->toArray();
            $testimonials = Testimonial::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)
                ->where($rangeFilter)->get()->toArray();
        } else {
            // Default: all records for the current session.
            $videos = Video::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)->get()->toArray();
            $images = Image::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)->get()->toArray();
            $completion = Completion::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)->get()->toArray();
            $distributions = Distribution::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)->get()->toArray();
            $testimonials = Testimonial::orderBy('updated_at', 'DESC')->whereIn('school_id', $schoolFilter)->get()->toArray();
        }
        $user = $this->usersForUploadedDataViews();
        $schools = StateService::schoolsQuery()->get()->toArray();
        return view('uploadedData.uploaded-data')
            ->with('videos', $videos)
            ->with('images', $images)
            ->with('completion', $completion)
            ->with('distributions', $distributions)
            ->with('testimonials', $testimonials)
            ->with('schools', $schools)
            ->with('user', $user);
    }

    public function trainerSchoolsData($id)
    {
        $trainer_data = User::where('id', $id)->with('videos', 'images', 'completions', 'distributions', 'asigned_schools')->first()->toArray();
        $district = StateService::districtsQuery()->get();
        $schools = StateService::schoolsQuery()->get();
        $cordinator = StateService::cordinatorsQuery()->get();

        $months_data = AsignedSchool::where('user_id', $id)->where('route_date', '!=', null)->select('id', 'created_at', 'end_date', 'status', 'route_date', 'end_date')
            ->get();
        $userArr = $this->buildSessionMonthCounts($months_data);

        return view('TrainersReporting.trainer-schools-details')
            ->with('trainer_data', $trainer_data)
            ->with('schools', $schools)
            ->with('cordinator', $cordinator)
            ->with('district', $district)
            ->with('userArr', $userArr);
    }

    public function schoolsReportingByDistricts()
    {
        $sessionId = $this->reportSessionId();
        $district = StateService::districtsQuery()->get()->toArray();
        $districtIds = $this->reportDistrictIds();

        $schoolsWithAssigned = DB::table('asigned_schools')
            ->join('schools', 'schools.id', '=', 'asigned_schools.school_name')
            ->where('asigned_schools.session_id', $sessionId)
            ->whereIn('schools.district_id', $districtIds ?: [0])
            ->select('schools.*', 'asigned_schools.*')
            ->get()->toArray();

        $months_data = AsignedSchool::where('status', 1)->select('id', 'created_at', 'end_date', 'status', 'route_date', 'end_date')
            ->get();
        $totalSchools = $this->buildSessionMonthCounts($months_data);

        $workingTrainers = AsignedSchool::whereIn('district', $districtIds ?: [0])->get()->unique('user_id')->toArray();
        $distribution = Distribution::get()->toArray();

        return view('districtReporting.by-districts')
            ->with('totalSchools', $totalSchools)
            ->with('distribution', $distribution)
            ->with('district', $district)
            ->with('workingTrainers', $workingTrainers)
            ->with('schoolsWithAssigned', $schoolsWithAssigned);
    }

    public function districtsData($id)
    {
        $districtRecord = District::find($id);
        if (!$districtRecord) {
            abort(404, 'District not found.');
        }

        $stateId = StateService::scopeStateId();
        if ($stateId && (int) $districtRecord->state_id !== (int) $stateId) {
            return redirect()
                ->route('schools-reporting')
                ->with('error', 'That district belongs to another state. Showing districts for the selected state.');
        }

        $asigned_schools = AsignedSchool::where('district', $id)->get()->toArray();
        $schools = School::where('district_id', $id)->get();
        $district = StateService::districtsQuery()->get()->toArray();

        $districtNmae = $districtRecord->district;
        foreach ($district as $dis) {
            if ($dis['id'] == $id) {
                $districtNmae = $dis['district'];
            }
        }

        $distribution = Distribution::where('district', $districtNmae)->get()->toArray();
        $trainers = User::where('state_id', StateService::scopeStateId())->get();
        $not_startde_schools = AsignedSchool::where('district', $id)->where('route_date', null)->get()->toArray();

        $ongoing_schools = AsignedSchool::where('district', $id)->where('route_date', '!=', null)->get()->toArray();


        $months_data = AsignedSchool::where('district', $id)->where('status', 1)->select('id', 'created_at', 'end_date', 'status', 'route_date', 'end_date')
            ->get();
        $totalSchools = $this->buildSessionMonthCounts($months_data);

        return view('districtReporting.district-data')
            ->with('asigned_schools', $asigned_schools)
            ->with('schools', $schools)
            ->with('district', $district)
            ->with('distribution', $distribution)
            ->with('trainers', $trainers)
            ->with('ongoing_schools', $ongoing_schools)
            ->with('totalSchools', $totalSchools)
            ->with('not_startde_schools', $not_startde_schools);
    }



    public function schoolAssignedStatus(Request $request)
    {
        $completion = User::findOrFail($request->cordinator_id);
        $completion->school_assigned_status = $request->status;
        $completion->save();
        return response()->json(['message' => 'User status updated successfully.']);
    }

    public function dataUploadStatus(Request $request)
    {
        $completion = User::findOrFail($request->cordinator_id);
        $completion->data_upload_status = $request->status;
        $completion->save();
        return response()->json(['message' => 'User status updated successfully.']);
    }

    public function trainerStatusDetail(Request $request)
    {
        $user = User::findOrFail($request->trainer_id);
        $user->active_status = $request->active_status;
        $user->save();
        return response()->json($user);
    }

    public function schoolPaidStatus(Request $request)
    {
        if (!AcademicSessionService::isWritableContext()) {
            return response()->json(['message' => 'Payments are locked for closed/archive sessions.'], 423);
        }

        $sessionId = $this->reportSessionId();
        AsignedSchool::withoutGlobalScopes()
            ->where('session_id', $sessionId)
            ->where('school_name', $request->school_id)
            ->update(['paid_status' => $request->paid_status]);

        return response()->json(['success' => true]);
    }

    public function advancePayment(Request $request)
    {
        $sessionId = $this->reportSessionId();
        $advance_payments = AdvancePayment::withoutGlobalScopes()
            ->where('session_id', $sessionId)
            ->whereIn('user_id', User::where('state_id', StateService::scopeStateId())->pluck('id')->all() ?: [0])
            ->get()->toArray();
        $trainers = User::where('state_id', StateService::scopeStateId())->get()->toArray();
        return view('admin.advance-payments')
            ->with('advance_payments', $advance_payments)
            ->with('trainers', $trainers);
    }

    public function addAdvancePayment(Request $request)
    {
        if (!AcademicSessionService::isWritableContext()) {
            return response()->json(['message' => 'Advance payments are locked for closed/archive sessions.'], 423);
        }

        $request->validate([
            'user' => 'required',
            'role' => 'required',
            'payment' => 'required',
            'paid_date' => 'required'
        ]);

        $advance_payments = new AdvancePayment;
        $advance_payments->user_id = $request->user;
        $advance_payments->role = $request->role;
        $advance_payments->payment = $request->payment;
        $advance_payments->payment_date = date('d-m-Y', strtotime($request->paid_date));
        $advance_payments->session_id = $this->reportSessionId();
        $advance_payments->save();
        return response()->json($advance_payments);
    }

    public function schoolCompleteStatus()
    {
        SessionUploadService::syncAssignmentStatuses();
    }

    public function addAssigndData(Request $request)
    {
        if ($request->school_name !== null) {
            $result = SchoolAssignmentService::assignSchools(
                $request->school_name,
                (int) Auth::user()->id,
                $request->input('district'),
                $request->input('block'),
                Auth::user()->id
            );

            if (isset($result['error'])) {
                return response()->json(['error' => $result['error']], 422);
            }

            return response()->json(['success' => 'Schools Add successfully.']);
        } else {
            return redirect()->back()->with('error', 'Somethig Wrong Please Check!');
        }
    }

    public function rejectedUC()
    {
        $schoolIds = StateService::schoolsQuery()->pluck('id')->all();
        $completion = Completion::orderBy('created_at', 'DESC')->whereIn('school_id', $schoolIds ?: [0])->where('completion_note', '!=', null)
            ->where('emergency_approved', 0)->get()->toArray();
        $user = $this->usersForUploadedDataViews();
        $schools = StateService::schoolsQuery()->get()->toArray();

        return view('uploadedData.rejected-uc')
            ->with('completion', $completion)
            ->with('schools', $schools)
            ->with('user', $user);
    }

    public function approvalPendingUC()
    {
        $schoolIds = StateService::schoolsQuery()->pluck('id')->all();
        $completion = Completion::orderBy('created_at', 'DESC')->whereIn('school_id', $schoolIds ?: [0])->where('status', 0)->where('completion_note', null)->get()->toArray();
        $user = $this->usersForUploadedDataViews();
        $schools = StateService::schoolsQuery()->get()->toArray();

        return view('uploadedData.approval-pending-uc')
            ->with('completion', $completion)
            ->with('schools', $schools)
            ->with('user', $user);
    }

    public function emergencyApprovedUC()
    {
        $schoolIds = StateService::schoolsQuery()->pluck('id')->all();
        $completion = Completion::orderBy('created_at', 'DESC')->whereIn('school_id', $schoolIds ?: [0])->where('emergency_approved', 1)->get()->toArray();
        $user = $this->usersForUploadedDataViews();
        $schools = StateService::schoolsQuery()->get()->toArray();

        return view('uploadedData.emergency-approved-uc')
            ->with('completion', $completion)
            ->with('schools', $schools)
            ->with('user', $user);
    }

    public function settings()
    {
        $states = State::orderByDesc('id')->get();
        $currentState = StateService::current();
        $sessions = AcademicSessionService::all();
        $currentSession = AcademicSessionService::current();
        $activeSession = AcademicSessionService::active();

        $holidayStateId = $currentState?->id ?? $states->first()?->id;
        $holidayDistricts = $holidayStateId
            ? District::where('state_id', $holidayStateId)->orderBy('district')->get()
            : collect();

        $holidays = $holidayStateId
            ? Holiday::with('district')
                ->where('state_id', $holidayStateId)
                ->orderBy('holiday_date', 'desc')
                ->get()
            : collect();

        $holidayMap = [];
        $workingMap = [];
        foreach ($holidays as $h) {
            $key = $h->holiday_date->format('Y-m-d');
            $label = $h->title ?: ($h->entry_type === Holiday::TYPE_WORKING ? 'Working' : 'Holiday');
            if ($h->district_id && $h->district) {
                $label .= ' ('.$h->district->district.')';
            }

            if ($h->entry_type === Holiday::TYPE_WORKING) {
                $workingMap[$key] = isset($workingMap[$key]) ? $workingMap[$key].' / '.$label : $label;
            } else {
                $holidayMap[$key] = isset($holidayMap[$key]) ? $holidayMap[$key].' / '.$label : $label;
            }
        }

        $districtsByState = District::orderBy('district')
            ->get(['id', 'district', 'state_id'])
            ->groupBy('state_id')
            ->map(fn ($items) => $items->values());

        return view('admin.settings', compact(
            'states',
            'currentState',
            'sessions',
            'currentSession',
            'activeSession',
            'holidays',
            'holidayMap',
            'workingMap',
            'holidayDistricts',
            'districtsByState'
        ));
    }

    public function holidays()
    {
        return redirect()->route('settings');
    }

    public function storeHoliday(Request $request)
    {
        $request->validate([
            'state_id' => 'required|exists:states,id',
            'scope' => 'required|in:state,district',
            'district_id' => 'nullable|required_if:scope,district|exists:districts,id',
            'holiday_date' => 'required|date',
            'title' => 'nullable|string|max:255',
        ]);

        $stateId = (int) $request->state_id;
        $districtId = $request->scope === 'district' ? (int) $request->district_id : null;

        if ($districtId) {
            $district = District::findOrFail($districtId);
            if ((int) $district->state_id !== $stateId) {
                return redirect()->route('settings')
                    ->with('error', 'Selected district does not belong to the selected state.')
                    ->withFragment('holidays-section');
            }
        }

        $exists = Holiday::where('state_id', $stateId)
            ->whereDate('holiday_date', $request->holiday_date)
            ->when(
                $districtId,
                fn ($q) => $q->where('district_id', $districtId),
                fn ($q) => $q->whereNull('district_id')
            )
            ->where(function ($q) {
                $q->where('entry_type', Holiday::TYPE_OFF)->orWhereNull('entry_type');
            })
            ->exists();

        if ($exists) {
            return redirect()->route('settings')
                ->with('error', 'This holiday already exists for the selected scope.')
                ->withFragment('holidays-section');
        }

        // Remove force-working override if any for same scope/date
        Holiday::where('state_id', $stateId)
            ->whereDate('holiday_date', $request->holiday_date)
            ->where('entry_type', Holiday::TYPE_WORKING)
            ->when(
                $districtId,
                fn ($q) => $q->where('district_id', $districtId),
                fn ($q) => $q->whereNull('district_id')
            )
            ->delete();

        Holiday::create([
            'holiday_date' => $request->holiday_date,
            'title' => $request->title,
            'entry_type' => Holiday::TYPE_OFF,
            'state_id' => $stateId,
            'district_id' => $districtId,
            'created_by' => Auth::id(),
        ]);

        StateService::setViewingStateId($stateId);

        return redirect()->route('settings')->with('success', 'Holiday added successfully.')->withFragment('holidays-section');
    }

    public function toggleHoliday(Request $request)
    {
        $request->validate([
            'state_id' => 'required|exists:states,id',
            'holiday_date' => 'required|date',
            'scope' => 'required|in:state,district',
            'district_id' => 'nullable|required_if:scope,district|exists:districts,id',
            'title' => 'nullable|string|max:255',
        ]);

        $stateId = (int) $request->state_id;
        $districtId = $request->scope === 'district' ? (int) $request->district_id : null;

        if ($districtId) {
            $district = District::findOrFail($districtId);
            if ((int) $district->state_id !== $stateId) {
                return response()->json(['message' => 'District does not belong to selected state.'], 422);
            }
        }

        $result = HolidayService::toggleDate(
            $request->holiday_date,
            $stateId,
            $districtId,
            $request->title
        );

        StateService::setViewingStateId($stateId);

        $holiday = $result['holiday'];

        $title = $holiday?->title
            ?: ($result['status'] === 'working' ? 'Working' : 'Off');

        return response()->json([
            'ok' => true,
            'action' => $result['action'],
            'status' => $result['status'],
            'date' => $request->holiday_date,
            'label' => $title,
            'holiday' => $holiday ? [
                'id' => $holiday->id,
                'date' => optional($holiday->holiday_date)->format('Y-m-d') ?: $request->holiday_date,
                'title' => $title,
                'entry_type' => $holiday->entry_type ?? $result['status'],
                'district_id' => $holiday->district_id,
                'scope_label' => $holiday->district_id
                    ? (District::find($holiday->district_id)?->district ?? 'District')
                    : 'All',
            ] : null,
        ]);
    }

    public function deleteHoliday($id)
    {
        Holiday::findOrFail($id)->delete();

        return redirect()->route('settings')->with('success', 'Holiday removed successfully.');
    }

    public function updateHoliday(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);
        $stateId = (int) ($holiday->state_id ?? StateService::scopeStateId());

        $request->validate([
            'scope' => 'required|in:state,district',
            'district_id' => 'nullable|required_if:scope,district|exists:districts,id',
            'holiday_date' => 'required|date',
            'title' => 'nullable|string|max:255',
        ]);

        $districtId = $request->scope === 'district' ? (int) $request->district_id : null;

        if ($districtId) {
            $district = District::findOrFail($districtId);
            if ((int) $district->state_id !== $stateId) {
                return redirect()->route('settings')
                    ->with('error', 'Selected district does not belong to this holiday\'s state.')
                    ->withFragment('holidays-section');
            }
        }

        $exists = Holiday::where('state_id', $stateId)
            ->where('id', '!=', $holiday->id)
            ->whereDate('holiday_date', $request->holiday_date)
            ->when(
                $districtId,
                fn ($q) => $q->where('district_id', $districtId),
                fn ($q) => $q->whereNull('district_id')
            )
            ->exists();

        if ($exists) {
            return redirect()->route('settings')
                ->with('error', 'This holiday already exists for the selected scope.')
                ->withFragment('holidays-section');
        }

        $holiday->update([
            'holiday_date' => $request->holiday_date,
            'title' => $request->title,
            'district_id' => $districtId,
        ]);

        return redirect()->route('settings')->with('success', 'Holiday updated successfully.');
    }

    public function holidaysList(Request $request)
    {
        $request->validate([
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
        ]);

        $stateId = $request->filled('state_id') ? (int) $request->state_id : StateService::scopeStateId();
        $districtId = $request->filled('district_id') ? (int) $request->district_id : null;

        $offs = HolidayService::offQuery($districtId, $stateId)
            ->orderBy('holiday_date')
            ->get(['holiday_date', 'title', 'district_id'])
            ->map(function ($holiday) {
                return [
                    'date' => $holiday->holiday_date->format('Y-m-d'),
                    'title' => $holiday->title,
                    'district_id' => $holiday->district_id,
                ];
            })
            ->unique('date')
            ->values();

        $workingDates = HolidayService::workingQuery($districtId, $stateId)
            ->orderBy('holiday_date')
            ->get(['holiday_date'])
            ->map(fn ($h) => $h->holiday_date->format('Y-m-d'))
            ->unique()
            ->values();

        return response()->json([
            'holidays' => $offs,
            'working_dates' => $workingDates,
        ]);
    }

    public function academicSessions()
    {
        return redirect()->route('settings');
    }

    public function storeAcademicSession(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:academic_sessions,name',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        AcademicSessionService::createNew(
            $request->name,
            $request->start_date,
            $request->end_date
        );

        return redirect()->route('settings')->with('success', 'Session '.$request->name.' created and activated. Previous session has been closed.');
    }

    public function activateAcademicSession($id)
    {
        $session = AcademicSessionService::activate((int) $id);

        return redirect()->route('settings')->with('success', 'Session '.$session->name.' is now active.');
    }

    public function closeAcademicSession($id)
    {
        $session = AcademicSessionService::close((int) $id);

        return redirect()->route('settings')->with('success', 'Session '.$session->name.' has been closed.');
    }

    public function switchAcademicSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
        ]);

        AcademicSessionService::setViewingSessionId((int) $request->session_id);

        $session = AcademicSession::findOrFail($request->session_id);

        return redirect()->back()->with('success', 'Now viewing session: '.$session->name);
    }

    public function resetAcademicSessionView()
    {
        AcademicSessionService::setViewingSessionId(null);

        return redirect()->back()->with('success', 'Switched back to active session view.');
    }

    public function states()
    {
        return redirect()->route('settings');
    }

    public function storeState(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:states,name',
            'code' => 'required|string|max:10|unique:states,code',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ]);

        $state = StateService::create($request->name, $request->code);

        if ($request->hasFile('logo')) {
            $state->update([
                'logo' => $request->file('logo')->store('states', 'public'),
            ]);
        }

        StateService::setViewingStateId($state->id);

        return redirect()->route('settings')->with('success', 'State '.$state->name.' created. Assign districts, trainers, and coordinators to this state.');
    }

    public function switchState(Request $request)
    {
        $request->validate([
            'state_id' => 'required|exists:states,id',
        ]);

        StateService::setViewingStateId((int) $request->state_id);

        $state = State::findOrFail($request->state_id);
        $previousPath = parse_url(url()->previous(), PHP_URL_PATH) ?? '';

        // Detail pages are bound to IDs from the previous state —
        // after switching, send the user to the matching list for the new state.
        if (preg_match('#/getData/\d+#', $previousPath)) {
            return redirect()
                ->route('add_trainers')
                ->with('success', 'Now viewing state: '.$state->name.'. Opened trainers list for this state.');
        }

        if (preg_match('#/districts_data/\d+#', $previousPath)) {
            return redirect()
                ->route('schools-reporting')
                ->with('success', 'Now viewing state: '.$state->name);
        }

        if (preg_match('#/trainer_data/\d+#', $previousPath)) {
            return redirect()
                ->route('logs')
                ->with('success', 'Now viewing state: '.$state->name);
        }

        if (preg_match('#/trainer_schools_data/\d+#', $previousPath)) {
            return redirect()
                ->route('trainers-schools-data')
                ->with('success', 'Now viewing state: '.$state->name);
        }

        return redirect()->back()->with('success', 'Now viewing state: '.$state->name);
    }

    public function resetStateView()
    {
        StateService::setViewingStateId(null);

        return redirect()->back()->with('success', 'Switched back to default state view.');
    }

    public function updateState(Request $request, $id)
    {
        $state = State::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:states,name,'.$state->id,
            'code' => 'required|string|max:10|unique:states,code,'.$state->id,
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'code' => strtoupper($request->code),
        ];

        if ($request->hasFile('logo')) {
            if ($state->logo && Storage::disk('public')->exists($state->logo)) {
                Storage::disk('public')->delete($state->logo);
            }
            $data['logo'] = $request->file('logo')->store('states', 'public');
        }

        $state->update($data);

        return redirect()->route('settings')->with('success', 'State updated to '.$state->name.'.');
    }

    private function approvedDeleteBlocked($record, string $label)
    {
        if ((int) ($record->status ?? 0) === 1) {
            return response()->json(['error' => 'Approved '.$label.' cannot be deleted.'], 403);
        }

        return null;
    }

    private function approveBlockedIfRejected($record, string $noteField, string $label)
    {
        if (!empty($record->{$noteField})) {
            return response()->json(['error' => 'Rejected '.$label.' must be re-uploaded before approval.'], 422);
        }

        return null;
    }

    private function deletePublicStorageFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Users for Uploaded By columns: state-scoped trainers plus admins
     * (admins often have null state_id and were previously missing → "—").
     */
    private function usersForUploadedDataViews()
    {
        $stateId = StateService::scopeStateId();

        return User::query()
            ->when($stateId, function ($q) use ($stateId) {
                $q->where(function ($inner) use ($stateId) {
                    $inner->where('state_id', $stateId)->orWhere('role', 1);
                });
            })
            ->get();
    }

    /**
     * Build month columns from the current academic session and count rows by end_date.
     *
     * @param  \Illuminate\Support\Collection|array  $rows
     * @return array<int, array{month: string, count: int}>
     */
    private function buildSessionMonthCounts($rows): array
    {
        $labels = AcademicSessionService::monthLabels();
        $counts = array_fill_keys(array_keys($labels), 0);

        foreach ($rows as $row) {
            $endDate = is_array($row) ? ($row['end_date'] ?? null) : ($row->end_date ?? null);
            $parsed = AcademicSessionService::parseEndDate($endDate);
            if (!$parsed) {
                continue;
            }
            $key = $parsed->format('Y-m');
            if (array_key_exists($key, $counts)) {
                $counts[$key]++;
            }
        }

        $result = [];
        foreach ($labels as $key => $label) {
            $result[] = [
                'month' => $label,
                'count' => $counts[$key],
            ];
        }

        return $result;
    }
}
