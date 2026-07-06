<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SchoolController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return "cleared!";
});

Route::get('login', function () {
    return view('auth.login');
})->name('login');

Route::get('add', function () {
    return view('admin.add');
})->name('add');



Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return "Storage linked!";
});


Route::get('/send-sms',[SmsController::class, 'sendMessage']);

Route::get('/dashboard',[Controller::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/t-dashboard',[Controller::class, 'uploadData'])->name('t-dashboard');
Route::get('/logs',[AdminController::class, 'trainersLogs'])->name('logs');
Route::get('/advance-payment',[AdminController::class, 'advancePayment'])->name('advance-payment');
Route::post('/add-advance-payment',[AdminController::class, 'addAdvancePayment'])->name('add-advance-payment');
Route::get('/add_trainers',[AdminController::class, 'addTrainers'])->name('add_trainers');
Route::get('districts_data/{id}',[AdminController::class, 'districtsData'])->name('districts_data');
Route::post('paid_status', [AdminController::class, 'salaryStatus'])->name('paid_status');
Route::get('/paid-schools',[AdminController::class, 'paidSchools'])->name('paid-schools');
Route::get('/unpaid-schools',[AdminController::class, 'unPaidSchools'])->name('unpaid-schools');
Route::get('/today-assigned',[AdminController::class, 'todayAssignedSchools'])->name('today-assigned');
Route::get('custom-date', [AdminController::class, 'todayAssignedSchools'])->name('custom-date');

Route::get('/route-plan-schools', [AdminController::class, 'routePlanSchools'])->name('route-plan-schools');
Route::get('route-plan-custom-date', [AdminController::class, 'routePlanSchools'])->name('route-plan-custom-date');

Route::get('/ongoing-schools',[AdminController::class, 'OnGoingTrainers'])->name('ongoing-schools');
Route::get('/not-workig-trainers',[AdminController::class, 'NotWorkingTrainers'])->name('not-workig-trainers');
Route::get('/trainers-schools-data',[AdminController::class, 'TrainersSchoolsData'])->name('trainers-schools-data');
Route::get('/claim-trainers',[AdminController::class, 'ClaimTraniers'])->name('claim-trainers');
Route::get('/cordinators',[AdminController::class, 'Cordinators'])->name('cordinators');
Route::post('create-cordinator',[AdminController::class, 'cordinatorStore'])->name('create-cordinator');
Route::get('cordinator_data/{id}',[AdminController::class, 'cordinatorData'])->name('cordinator_data');

Route::get('/school-assigned-status', [AdminController::class, 'schoolAssignedStatus']);
Route::get('/data-upload-status', [AdminController::class, 'dataUploadStatus']);

Route::post('/blockdata',[Controller::class, 'blockData'])->name('blockdata');
Route::post('/schooldata',[Controller::class, 'schoolData'])->name('schooldata');
Route::get('/trainer-reporting',[Controller::class, 'CordinatorTrainerReporting'])->name('trainer-reporting');
Route::get('getData/{id}',[AdminController::class, 'trainerDetail']);
Route::post('create-trainer',[ProfileController::class, 'trainerStore'])->name('create-trainer');
Route::post('video-data',[AdminController::class, 'videoData'])->name('video-data');
Route::post('update-trainer',[ProfileController::class, 'updateData'])->name('update-trainer');

Route::post('add-schools-new',[AdminController::class, 'addAssigndData'])->name('add-schools-new');

Route::post('create-data',[Controller::class, 'stoteInstructorData'])->name('create-data');

Route::get('upload-data/{id}',[Controller::class, 'trainerData']);
Route::post('route-plan/{id}',[Controller::class, 'uploadRoutePlan'])->name('route-plan');

Route::get('custom-data', [AdminController::class, 'trainersLogs'])->name('custom-data');

Route::get('/video-status', [AdminController::class, 'videoStatus']);
Route::get('/trainer-status', [AdminController::class, 'trainerStatusDetail']);
Route::get('/school-paid-status', [AdminController::class, 'schoolPaidStatus']);
Route::get('1stvideo/{id}',[AdminController::class, 'fstvideoDetail'])->name('1stvideo');
Route::get('2ndvideo/{id}',[AdminController::class, 'sndvideoDetail'])->name('2ndvideo');
Route::get('completion-remove/{id}/{sid}',[AdminController::class, 'completionDetail'])->name('completion-remove');
Route::get('distribution-remove/{id}',[AdminController::class, 'distributionDetail'])->name('distribution-remove');

Route::get('images/{id}/{imgid}',[AdminController::class, 'imagesDetail'])->name('images');
Route::get('delete-images/{id}/{sid}',[AdminController::class, 'deleteImages'])->name('delete-images');
Route::get('delete-videos/{id}/{sid}',[AdminController::class, 'deleteVideos'])->name('delete-videos');
//Notes
Route::post('video-note',[AdminController::class, 'videoNote'])->name('video-note');
Route::post('image-note',[AdminController::class, 'imageNote'])->name('image-note');
Route::post('distribution-note',[AdminController::class, 'distributionNote'])->name('distribution-note');
Route::post('completion-note',[AdminController::class, 'completionNote'])->name('completion-note');
Route::post('claim-note',[Controller::class, 'trainerClaimNote'])->name('claim-note');
// Notes End
Route::get('custom-date-data', [AdminController::class, 'uploadedData'])->name('custom-date-data');
Route::get('uploaded-data',[AdminController::class, 'uploadedData'])->name('uploaded-data');
Route::get('trainer_data/{id}',[AdminController::class, 'trainerData'])->name('trainer_data');
Route::post('remark/{id}',[AdminController::class, 'remarkNote'])->name('remark');
Route::delete('a-school/{id}/{sid}', [AdminController::class, 'asignedSchoolDelete'])->name('a-school');
Route::get('trainer_schools_data/{id}',[AdminController::class, 'trainerSchoolsData'])->name('trainer_schools_data');
Route::get('schools-reporting',[AdminController::class, 'schoolsReportingByDistricts'])->name('schools-reporting');
Route::get('trainers-reporting',[AdminController::class, 'trainersReporting'])->name('trainers-reporting');

Route::get('/image-status', [AdminController::class, 'imageStatus']);
Route::get('/completion-status', [AdminController::class, 'completionStatus']);
Route::get('/distributions-status', [AdminController::class, 'distributionsStatus']);
Route::get('/salary_status', [AdminController::class, 'salaryStatus']);
Route::get('schools',[AdminController::class, 'getSchools'])->name('schools');

Route::get('rejected-uc',[AdminController::class, 'rejectedUC'])->name('rejected-uc');
Route::get('approval-pending-uc',[AdminController::class, 'approvalPendingUC'])->name('approval-pending-uc');
Route::get('emergency-approved-uc',[AdminController::class, 'emergencyApprovedUC'])->name('emergency-approved-uc');

Route::get('/holidays/list', [AdminController::class, 'holidaysList'])->name('holidays.list');
Route::get('/holidays', [AdminController::class, 'holidays'])->name('holidays');
Route::post('/holidays', [AdminController::class, 'storeHoliday'])->name('holidays.store');
Route::put('/holidays/{id}', [AdminController::class, 'updateHoliday'])->name('holidays.update');
Route::delete('/holidays/{id}', [AdminController::class, 'deleteHoliday'])->name('holidays.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/schools/add', [SchoolController::class, 'create'])->name('schools.create');
Route::post('/schools/store', [SchoolController::class, 'store'])->name('schools.store');

Route::get('/districts/fetch', [SchoolController::class, 'fetchDistricts'])->name('districts.fetch');
Route::post('/districts/store', [SchoolController::class, 'storeDistrict'])->name('districts.store');

Route::get('/blocks/fetch/{district_id}', [SchoolController::class, 'fetchBlocks'])->name('blocks.fetch');
Route::post('/blocks/store', [SchoolController::class, 'storeBlock'])->name('blocks.store');
Route::post('/district/store', [SchoolController::class, 'storeDistrict'])->name('store.district');


// Add multiple school
Route::post('/schools/import', [SchoolController::class, 'import'])->name('schools.import');
Route::get('/schools/import-form', [SchoolController::class, 'showImportForm']);
Route::get('/schools/download-template', [SchoolController::class, 'downloadTemplate'])->name('schools.download-template');
Route::get('/schools/export', [SchoolController::class, 'exportSchools'])->name('schools.export');

Route::get('/admin/manageschool', [SchoolController::class, 'manageSchools'])->name('admin.manageschool');
Route::get('/admin/schools/filter/{district_id}', [SchoolController::class, 'filterByDistrict'])->name('admin.schools.filter');
Route::post('/admin/schools/delete', [SchoolController::class, 'delete'])->name('admin.schools.delete');
Route::get("/update/{id}", [SchoolController::class, 'update']);
Route::post("/update-school", [SchoolController::class, 'updateschool']);


Route::delete('/admin/delete-district/{i    d}', [SchoolController::class, 'deleteDistrict'])->name('admin.deleteDistrict');
Route::delete('/admin/delete-block/{id}', [SchoolController::class, 'deleteBlock'])->name('admin.deleteBlock');
Route::get('/admin/blocks/{district_id}', [SchoolController::class, 'fetchBlocks'])->name('admin.getBlocks');


