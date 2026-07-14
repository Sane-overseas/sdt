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
use App\Models\Video;
use App\Models\Image;
use App\Models\Distribution;
use App\Models\Completion;
use App\Models\District;
use App\Models\School;
use App\Models\Block;
use App\Models\User;
use App\Models\Cordinator;
use App\Models\AsignedSchool;
use App\Models\PaidSchool;
use App\Services\AcademicSessionService;
use App\Services\HolidayService;
use App\Services\SessionUploadService;
use App\Services\StateService;
use View;
use Session;
use Config;
use Carbon\Carbon;
use DateTime;
use DB;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $schools = StateService::schoolsQuery()->get()->toArray();

        if (Auth::user() == null) {
            return redirect()->route('login');
        }elseif(Auth::user()->role == 0){

            $activeSession = AcademicSessionService::active();

            $a1 = AsignedSchool::where('user_id', Auth::user()->id)->where('status', 0)
            ->where('end_date', '<=', Carbon::yesterday()->format('d-m-Y'))
            ->get()->toArray();
            $a2 = AsignedSchool::where('user_id', Auth::user()->id)->where('status', 0)
            ->where('end_date', '>=', Carbon::yesterday()->format('d/m/y'))
            ->get()->toArray();

            $asigned_schools = array_merge($a1,$a2);

            $videos = Video::where('user_id', Auth::user()->id)->get()->toArray();
            $images = Image::where('user_id', Auth::user()->id)->get()->toArray();
            $completion = Completion::where('user_id', Auth::user()->id)->get()->toArray();
            $distribution = Distribution::where('user_id', Auth::user()->id)->get()->toArray();

            $first_time_login = true;
            $schools = StateService::schoolsQuery()->get();
            $user = User::where('id', Auth::user()->id)->with('asigned_schools')->first()->toArray();
            return view('trainer.upload_data')
            ->with('asigned_schools', $asigned_schools)
            ->with('first_time_login', $first_time_login)
            ->with('user', $user)
            ->with('videos', $videos)
            ->with('images', $images)
            ->with('completion', $completion)
            ->with('distribution', $distribution)
            ->with('schools', $schools)
            ->with('activeAcademicSession', $activeSession);
        }elseif(Auth::user()->role == 2){
             $activeSession = AcademicSessionService::active();

             $a1 = AsignedSchool::where('user_id', Auth::user()->id)->where('status', 0)
            ->where('end_date', '<=', Carbon::yesterday()->format('d-m-Y'))
            ->get()->toArray();
            $a2 = AsignedSchool::where('user_id', Auth::user()->id)->where('status', 0)
            ->where('end_date', '>=', Carbon::yesterday()->format('d/m/y'))
            ->get()->toArray();

            $asigned_schools = array_merge($a1,$a2);
            $schools = StateService::schoolsQuery()->get();
            $user = User::where('id', Auth::user()->id)->with('asigned_schools')->first()->toArray();
            return view('trainer.upload_data')
            ->with('asigned_schools', $asigned_schools)
            ->with('user', $user)
            ->with('schools', $schools)
            ->with('activeAcademicSession', $activeSession);
        }elseif(Auth::user()->role == 4){
            $trainer_data = User::where('id' , Auth::user()->id)->with('videos', 'images' ,'completions', 'distributions','asigned_schools')->first()->toArray();
            $district = StateService::districtsQuery()->get();
            $school = StateService::schoolsQuery()->get();
            $cordinator = StateService::cordinatorsQuery()->get();
            $user = User::where('state_id', StateService::scopeStateId())->get()->toArray();
            return view('admin.upload_data_new')
            ->with('trainer_data', $trainer_data)
            ->with('school', $school)
            ->with('cordinator', $cordinator)
            ->with('district', $district)
            ->with('user', $user);
        }else{
            $trainers = User::where('role' ,'!=', 1)->where('state_id', StateService::scopeStateId())->get();
            $cordinator = StateService::cordinatorsQuery()->get();
            $schoolScope = StateService::schoolsQuery();
            $schoolIds = $schoolScope->pluck('id')->all();
            $totalStudents = StateService::schoolsQuery()->sum('total_students');
            $completeStudents = Distribution::sum('complete_students');
            // Schools are master data; keep counts same across sessions.
            $totalScholls = StateService::schoolsQuery()->count();
            $completeScholls = StateService::schoolsQuery()->where('status' , 1)->count();
            $distribution = Distribution::get()->count();
            $trainersWithAssigned = User::where('state_id', StateService::scopeStateId())->with('asigned_schools')->get()->toArray();
            $ucReceived = Completion::whereIn('school_id', $schoolIds ?: [0])->count();
            $notAssignrdUc = Completion::whereIn('school_id', $schoolIds ?: [0])->where('status', 0)->where('completion_note', null)->count();
            $approvedUc = Completion::whereIn('school_id', $schoolIds ?: [0])->where('status', 1)->count();
            $districtIds = StateService::districtIds();
            $totalasignedSchools = AsignedSchool::whereIn('district', $districtIds ?: [0])->count();
            $activeAsignedSchools = AsignedSchool::whereIn('district', $districtIds ?: [0])->where('route_date', '!=' ,null)->count();
            $asignedSchools =  AsignedSchool::whereIn('district', $districtIds ?: [0])->get();
            $paidSchools = PaidSchool::whereIn('school_id', $schoolIds ?: [0])->count();
            $holdTrainers = User::where('state_id', StateService::scopeStateId())->where('active_status' , 0)->count();
            $rejecteduc = Completion::whereIn('school_id', $schoolIds ?: [0])->where('completion_note','!=', null)->where('emergency_approved', 0)->count();

            return view('dashboard')
            ->with('totalStudents', $totalStudents)
            ->with('completeStudents', $completeStudents)
            ->with('totalScholls', $totalScholls)
            ->with('completeScholls', $completeScholls)
            ->with('trainersWithAssigned', $trainersWithAssigned)
            ->with('totalasignedSchools', $totalasignedSchools)
            ->with('activeAsignedSchools', $activeAsignedSchools)
            ->with('distribution', $distribution)
            ->with('ucReceived', $ucReceived)
            ->with('trainers', $trainers)
            ->with('notAssignrdUc', $notAssignrdUc)
            ->with('approvedUc', $approvedUc)
            ->with('asignedSchools', $asignedSchools)
            ->with('paidSchools', $paidSchools)
            ->with('holdTrainers', $holdTrainers)
            ->with('cordinator', $cordinator)
            ->with('rejecteduc', $rejecteduc);
        }

    }

    public function uploadData()
    {
        $this->schoolCompleteStatus();
         $schools = StateService::schoolsQuery()->get();
        if(Auth::user() == null) {
            return redirect()->route('login');
        }else{

            $user = User::where('id', Auth::user()->id)->with('asigned_schools')->first()->toArray();
            $videos = Video::where('user_id', Auth::user()->id)->get()->toArray();
            $images = Image::where('user_id', Auth::user()->id)->get()->toArray();
            $completion = Completion::where('user_id', Auth::user()->id)->get()->toArray();
            $distribution = Distribution::where('user_id', Auth::user()->id)->get()->toArray();
            $district = StateService::districtsQuery()->get()->toArray();
            $cordinators = Cordinator::where('id' , Auth::user()->cordinator_id )->first()->toArray();
            $activeSession = AcademicSessionService::active();
            return view('trainer.dashboard')
            ->with('user', $user)
            ->with('videos', $videos)
            ->with('images', $images)
            ->with('completion', $completion)
            ->with('distribution', $distribution)
            ->with('district', $district)
            ->with('cordinators', $cordinators)
            ->with('schools', $schools)
            ->with('activeAcademicSession', $activeSession);
        }
    }

    public function blockData(Request $request)
    {
        StateService::assertDistrictInScope((int) $request->id);
        $block['block'] =  Block::where('district_id' ,$request->id )->get(["block", "id"]);
        return response()->json($block);
    }

    public function schoolData(Request $request)
    {
        $assignedIds = \App\Services\SchoolAssignmentService::assignedSchoolIdsForActiveSession();

        $school['school'] = StateService::schoolsQuery()
            ->where('block', $request->value)
            ->whereNotIn('id', $assignedIds)
            ->get(['school_name', 'asigned_school', 'id']);

        return response()->json($school);
    }

    public function trainerClaimNote(Request $request)
    {
        $request->validate([
            'claim_note' => 'required'
        ]);
        $routeData = User::findOrFail($request->id);
        $routeData->claim_note = $request->claim_note;
        $routeData->salary_status = 0;
        $routeData->save();
        return redirect()->back();
    }

    public function CordinatorTrainerReporting()
    {
        $cordinator_trainers = User::where('cordinator_id' ,  Auth::user()->cordinator_id)->get();
        foreach ($cordinator_trainers as $trainer) {
            $trainer->setRelation(
                'asigned_schools',
                AsignedSchool::withoutGlobalScopes()->where('user_id', $trainer->id)->get()
            );
        }
        $cordinator_trainers = $cordinator_trainers->toArray();

        $totalScholls = 0;
        $completeSchools = 0;
        $pendingSchools = 0;
        $notstartedSchools = 0;
        foreach($cordinator_trainers as $trainers){
            foreach($trainers['asigned_schools'] as $tr){
               $totalScholls++;
               if($tr['status'] == 1){
                  $completeSchools++;
               }
               if($tr['route_date'] != null && $tr['status'] == 0){
                  $pendingSchools++;
               }
               if($tr['route_date'] == null){
                  $notstartedSchools++;
               }
            }
        }
        $schools = StateService::schoolsQuery()->get();
        $district = StateService::districtsQuery()->get()->toArray();
        return view('admin.cordinator-trainers')
        ->with('cordinator_trainers', $cordinator_trainers)
        ->with('totalScholls', $totalScholls)
        ->with('completeSchools', $completeSchools)
        ->with('pendingSchools', $pendingSchools)
        ->with('notstartedSchools', $notstartedSchools)
        ->with('schools', $schools)
        ->with('district', $district);
    }

    public function stoteInstructorData(Request $request)
    {
        if (!$request->file()) {
            return response()->json(['message' => 'Please Select Any File.'], 404);
        }

        try {
            $assignment = SessionUploadService::assertCanUpload(
                (int) $request->input('id'),
                (int) $request->input('user_id')
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        if($request->hasfile('fst_videos') || $request->hasfile('snd_videos')){

                $request->validate([
                    'fst_videos' => 'mimes:mp4',
                    'snd_videos' => 'mimes:mp4',
                ]);

                $user = Video::where('user_id', $request->input('user_id'))->where('school_name', $request->school_name)->first();
                if($user == null){
                    $video = new Video();
                    $video->user_id = $request->input('user_id');
                    $video->uploaded_user =  Auth::user()->id ;
                    $video->cordinator = $request->input('cordinator');
                    $video->district = $request->input('district');
                    $video->bloack = $request->input('block');
                    $video->school_name = $request->input('school_name');
                    $video->school_address = $request->input('school_address');
                    $video->intime = $request->input('intime');
                    $video->outtime = $request->input('outtime');
                    $video->route_date = $request->input('route_date');
                    $video->school_id = $request->input('school_id');
                    $video->session_id = AcademicSessionService::assignmentSessionId();
                    if($request->file('fst_videos')){
                        $videoName = Auth::user()->instructor_code.'_'.'fst_videos'.'_'.$request->file('fst_videos')->getClientOriginalName();
                        $videoPath = $request->file('fst_videos')->storeAs('videos', $videoName, 'public');
                        $video->fst_video = $videoName;
                        $video->created_date = date('d-m-y - h:i');
                    }else{
                        $videoName = Auth::user()->instructor_code.'_'.'snd_videos'.'_'.$request->file('snd_videos')->getClientOriginalName();
                        $videoPath = $request->file('snd_videos')->storeAs('videos', $videoName, 'public');
                        $video->snd_video = $videoName;
                        $video->created_date = date('d-m-y - h:i');
                    }
                    $video->save();
                }else{
                    $video_update = Video::find($user->id);
                    if($request->file('fst_videos')){
                        if($user['fst_videos'] == null){
                            $video_update->video_note = null;
                        }
                        $videoName = Auth::user()->instructor_code.'_'.'fst_videos'.'_'.$request->file('fst_videos')->getClientOriginalName();
                        $videoPath = $request->file('fst_videos')->storeAs('videos', $videoName, 'public');
                        $video_update->fst_video = $videoName;
                        $video_update->update();
                    }else{
                        if($user['snd_videos'] == null){
                            $video_update->video_note = null;
                        }
                        $videoName = Auth::user()->instructor_code.'_'.'snd_videos'.'_'.$request->file('snd_videos')->getClientOriginalName();
                        $videoPath = $request->file('snd_videos')->storeAs('videos', $videoName, 'public');
                        $video_update->snd_video = $videoName;
                        $video_update->update();
                    }
                }
            }

            if($request->hasfile('ifsb_image') || $request->hasfile('group_image') || $request->hasfile('fst_aimage') || $request->hasfile('snd_aimage') || $request->hasfile('trd_aimage')){

                $request->validate([
                    'ifsb_image' => 'mimes:jpeg,jpg,png',
                    'group_image' => 'mimes:jpeg,jpg,png',
                    'fst_aimage' => 'mimes:jpeg,jpg,png',
                    'snd_aimage' => 'mimes:jpeg,jpg,png',
                    'trd_aimage' => 'mimes:jpeg,jpg,png',
                ]);

                $user = Image::where('user_id', $request->input('user_id'))->where('school_name', $request->school_name)->first();
                if($user == null){
                    $image = new Image();
                    $image->user_id = $request->input('user_id');
                    $image->uploaded_user =  Auth::user()->id ;
                    $image->cordinator = $request->input('cordinator');
                    $image->district = $request->input('district');
                    $image->bloack = $request->input('block');
                    $image->school_name = $request->input('school_name');
                    $image->school_address = $request->input('school_address');
                    $image->intime = $request->input('intime');
                    $image->outtime = $request->input('outtime');
                    $image->route_date = $request->input('route_date');
                    if($request->hasfile('ifsb_image')){
                        $imageName = Auth::user()->instructor_code.'_'.'ifsb_image'.'_'.$request->file('ifsb_image')->getClientOriginalName();
                        $imagePath = $request->file('ifsb_image')->storeAs('images', $imageName, 'public');
                        $image->ifsb_image = $imageName;
                    }elseif ($request->hasfile('group_image')) {
                        $imageName = Auth::user()->instructor_code.'_'.'group_image'.'_'.$request->file('group_image')->getClientOriginalName();
                        $imagePath = $request->file('group_image')->storeAs('images', $imageName, 'public');
                        $image->group_image = $imageName;
                    }elseif ($request->hasfile('fst_aimage')) {
                        $imageName = Auth::user()->instructor_code.'_'.'fst_aimage'.'_'.$request->file('fst_aimage')->getClientOriginalName();
                        $imagePath = $request->file('fst_aimage')->storeAs('images', $imageName, 'public');
                        $image->fst_aimage = $imageName;
                    }elseif ($request->hasfile('snd_aimage')) {
                        $imageName = Auth::user()->instructor_code.'_'.'snd_aimage'.'_'.$request->file('snd_aimage')->getClientOriginalName();
                        $imagePath = $request->file('snd_aimage')->storeAs('images', $imageName, 'public');
                        $image->snd_aimage = $imageName;
                    }else{
                        $imageName = Auth::user()->instructor_code.'_'.'trd_aimage'.'_'.$request->file('trd_aimage')->getClientOriginalName();
                        $imagePath = $request->file('trd_aimage')->storeAs('images', $imageName, 'public');
                        $image->trd_aimage = $imageName;
                    }
                    $image->created_date = date('d-m-y - h:i');
                    $image->school_id = $request->input('school_id');
                    $image->session_id = AcademicSessionService::assignmentSessionId();
                    $image->save();
                }else{
                    $image_update = Image::find($user->id);
                    if($request->hasfile('ifsb_image')){
                        if($user['ifsb_image'] == null){
                            $image_update->image_note = null;
                        }
                        $imageName = Auth::user()->instructor_code.'_'.'ifsb_image'.'_'.$request->file('ifsb_image')->getClientOriginalName();
                        $imagePath = $request->file('ifsb_image')->storeAs('images', $imageName, 'public');
                        $image_update->ifsb_image = $imageName;
                    }elseif ($request->hasfile('group_image')) {
                        if($user['group_image'] == null){
                            $image_update->image_note = null;
                        }
                        $imageName = Auth::user()->instructor_code.'_'.'group_image'.'_'.$request->file('group_image')->getClientOriginalName();
                        $imagePath = $request->file('group_image')->storeAs('images', $imageName, 'public');
                        $image_update->group_image = $imageName;
                    }elseif ($request->hasfile('fst_aimage')) {
                        if($user['fst_aimage'] == null){
                            $image_update->image_note = null;
                        }
                        $imageName = Auth::user()->instructor_code.'_'.'fst_aimage'.'_'.$request->file('fst_aimage')->getClientOriginalName();
                        $imagePath = $request->file('fst_aimage')->storeAs('images', $imageName, 'public');
                        $image_update->fst_aimage = $imageName;
                    }elseif ($request->hasfile('snd_aimage')) {
                        if($user['snd_aimage'] == null){
                            $image_update->image_note = null;
                        }
                        $imageName = Auth::user()->instructor_code.'_'.'snd_aimage'.'_'.$request->file('snd_aimage')->getClientOriginalName();
                        $imagePath = $request->file('snd_aimage')->storeAs('images', $imageName, 'public');
                        $image_update->snd_aimage = $imageName;
                    }else{
                        if($user['trd_aimage'] == null){
                            $image_update->image_note = null;
                        }
                        $imageName = Auth::user()->instructor_code.'_'.'trd_aimage'.'_'.$request->file('trd_aimage')->getClientOriginalName();
                        $imagePath = $request->file('trd_aimage')->storeAs('images', $imageName, 'public');
                        $image_update->trd_aimage = $imageName;
                    }
                    $image_update->created_date = date('d-m-y - h:i');
                    $image_update->update();
                }
            }

            if($request->hasfile('completion_file')){
                $request->validate([
                    'completion_file' => 'mimes:jpeg,jpg,png,pdf',
                ]);
                $user = Completion::where('user_id', $request->input('user_id'))->where('school_name', $request->school_name)->first();
                $route_date = explode('-' , $request->input('route_date'));
                if($user == null){
                    $completion = new Completion();
                    $completion->user_id = $request->input('user_id');
                    $completion->uploaded_user =  Auth::user()->id ;
                    $completion->cordinator = $request->input('cordinator');
                    $completion->district = $request->input('district');
                    $completion->bloack = $request->input('block');
                    $completion->school_name = $request->input('school_name');
                    $completion->school_address = $request->input('school_address');
                    $completion->intime = $request->input('intime');
                    $completion->outtime = $request->input('outtime');
                    $completionName = Auth::user()->instructor_code.'_'.$request->file('completion_file')->getClientOriginalName();
                    $completionPath = $request->file('completion_file')->storeAs('completion', $completionName, 'public');
                    $completion->completion_file = $completionName;
                    $completion->route_date = $request->input('route_date');
                    $completion->created_date = date('d-m-y - h:i');
                    $completion->school_id = $request->input('school_id');
                    $completion->end_date = trim($route_date[1]);
                    $completion->session_id = AcademicSessionService::assignmentSessionId();
                    $completion->save();

                    $uc_submitted = AsignedSchool::find($assignment->id);
                    $uc_submitted->uc_submitted = 1;
                    $uc_submitted->save();

                }else{
                    $completion = Completion::find($user->id);
                    $completionName = Auth::user()->instructor_code.'_'.$request->file('completion_file')->getClientOriginalName();
                    $completionPath = $request->file('completion_file')->storeAs('completion', $completionName, 'public');
                    $completion->completion_file = $completionName;
                    $completion->status = 0;
                    $completion->completion_note = null;
                    $completion->update();

                    $school_uc = School::find($request->input('school_id'));
                    $school_uc->completion_status = 0;
                    $school_uc->status = 0;
                    $school_uc->save();

                    $uc_submitted = AsignedSchool::find($assignment->id);
                    $uc_submitted->uc_submitted = 1;
                    $uc_submitted->status = 0;
                    $uc_submitted->save();
                }
            }

            if($request->hasfile('distribution_file')){

                $request->validate([
                    'complete_students' => 'required',
                    'distribution_file' => 'mimes:jpeg,jpg,png,pdf',
                ]);

                $user = Distribution::where('user_id', $request->input('user_id'))->where('school_name', $request->school_name)->first();
                if($user == null){
                    $distribution = new Distribution();
                    $distribution->user_id =  $request->input('user_id');
                    $distribution->uploaded_user =  Auth::user()->id ;
                    $distribution->cordinator = $request->input('cordinator');
                    $distribution->district = $request->input('district');
                    $distribution->bloack = $request->input('block');
                    $distribution->school_name = $request->input('school_name');
                    $distribution->school_address = $request->input('school_address');
                    $distribution->intime = $request->input('intime');
                    $distribution->outtime = $request->input('outtime');
                    $distributionName = Auth::user()->instructor_code.'_'.$request->file('distribution_file')->getClientOriginalName();
                    $distributionPath = $request->file('distribution_file')->storeAs('distribution', $distributionName, 'public');
                    $distribution->distribution_file = $distributionName;
                    $distribution->created_date = date('d-m-y - h:i');
                    $distribution->route_date = $request->input('route_date');
                    $distribution->complete_students = $request->input('complete_students');
                    $distribution->school_id = $request->input('school_id');
                    $distribution->session_id = AcademicSessionService::assignmentSessionId();
                    $distribution->save();
                }else{
                    $distribution = Distribution::find($user->id);
                    $distributionName = Auth::user()->instructor_code.'_'.$request->file('distribution_file')->getClientOriginalName();
                    $distributionPath = $request->file('distribution_file')->storeAs('distribution', $distributionName, 'public');
                    $distribution->complete_students = $request->input('complete_students');
                    $distribution->distribution_file = $distributionName;
                    $distribution->distribution_note = null;
                    $distribution->update();
                }
            }
        SessionUploadService::syncAssignmentStatuses();
        return Response::json();
    }

    public function trainerData($id)
    {
        try {
            $assignment = SessionUploadService::assertCanUpload((int) $id, Auth::id());
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }

        $school_data = $assignment->toArray();
        // $cordinators = Cordinator::where('id' , Auth::user()->cordinator_id )->first()->toArray();
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $cordinatorId = Auth::user()->cordinator_id;
        if (!$cordinatorId) {
            return response()->json(['error' => 'Cordinator ID not found in user'], 404);
        }

        $cordinator = Cordinator::find($cordinatorId);
        if (!$cordinator) {
            return response()->json(['error' => 'Cordinator not found'], 404);
        }

        $cordinators = $cordinator->toArray();


        $district = StateService::districtsQuery()->get();
        $block = Block::get();
        $schools = StateService::schoolsQuery()->get();

        foreach($schools as $school){
            if($school['id'] == $school_data['school_name']){
                $schoolName = $school['school_name'];
            }
        }

        $asch = AsignedSchool::where('id' , $id)->first()->toArray();

        $user_videos = Video::where('user_id', $asch['user_id'])->where('school_name', $schoolName)->first();

        $user_images = Image::where('user_id', $asch['user_id'])->where('school_name', $schoolName)->first();

        $user_completion = Completion::where('user_id', $asch['user_id'])->where('school_name', $schoolName)->first();
        $user_distribution = Distribution::where('user_id', $asch['user_id'])->where('school_name', $schoolName)->first();

        return view('trainer.add_data')
        ->with('user_images', $user_images)
        ->with('user_videos', $user_videos)
        ->with('user_completion', $user_completion)
        ->with('user_distribution', $user_distribution)
        ->with('school_data', $school_data)
        ->with('cordinators', $cordinators)
        ->with('district', $district)
        ->with('schools', $schools)
        ->with('block', $block)
        ->with('activeAcademicSession', AcademicSessionService::active());
    }

    public function uploadRoutePlan(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'working_days' => 'required|integer|min:1',
            'intime' => 'required',
            'outtime' => 'required',
        ]);

        try {
            SessionUploadService::assertCanSetRoutePlan((int) $request->id);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $startDate = Carbon::parse($request->start_date);
        $routeData = AsignedSchool::findOrFail($request->id);
        $school = School::find($routeData->school_name);
        $districtId = $school?->district_id ? (int) $school->district_id : null;
        $stateId = $school?->district?->state_id ? (int) $school->district->state_id : null;

        if (HolidayService::isHoliday($startDate, $districtId, $stateId)) {
            return redirect()->back()->with('error', 'Start date cannot be on a holiday ('.HolidayService::holidayLabel($startDate, $districtId, $stateId).'). Please choose a working day.');
        }

        $workingDays = (int) $request->working_days;
        $endDate = HolidayService::calculateEndDate($startDate, $workingDays, $districtId, $stateId);

        $routeData->route_date = $startDate->format('d/m/Y')." - ".$endDate->format('d/m/Y');
        $routeData->start_route_plan = date('H:i', strtotime($request->intime));
        $routeData->end_route_plan = date('H:i', strtotime($request->outtime));
        $routeData->end_date = $endDate->format('d-m-Y');
        $routeData->working_days = $workingDays;
        $routeData->add_route_plan_date = date('Y-m-d H:i:s');
        $routeData->added_by_route_plan = Auth::user()->id;
        $routeData->save();

        return redirect()->back()->with('success', 'Route plan saved. '.$workingDays.' working days (holidays excluded).');
    }

    public function schoolCompleteStatus()
    {
        SessionUploadService::syncAssignmentStatuses();
    }
}
