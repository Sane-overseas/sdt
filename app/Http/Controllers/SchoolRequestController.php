<?php

namespace App\Http\Controllers;

use App\Models\AsignedSchool;
use App\Models\School;
use App\Services\SchoolRequestService;
use App\Services\StateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class SchoolRequestController extends BaseController
{
    /** Trainer: list available schools in block + current requests. */
    public function trainerIndex()
    {
        $user = Auth::user();
        if (!$user || !in_array((int) $user->role, [0, 2], true)) {
            abort(403);
        }

        $available = SchoolRequestService::availableSchoolsForTrainer($user);
        $remaining = SchoolRequestService::remainingSlots((int) $user->id);
        $myRequests = AsignedSchool::with([])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $schoolNames = School::whereIn('id', $myRequests->pluck('school_name')->filter()->all())
            ->pluck('school_name', 'id');

        return view('trainer.school-requests', compact('available', 'remaining', 'myRequests', 'schoolNames', 'user'));
    }

    /** Trainer: submit school selection (max remaining slots). */
    public function trainerStore(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array((int) $user->role, [0, 2], true)) {
            abort(403);
        }

        $request->validate([
            'school_ids' => 'required|array|min:1',
            'school_ids.*' => 'integer|exists:schools,id',
        ]);

        $result = SchoolRequestService::requestSchools($user, $request->input('school_ids', []));

        if (!empty($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        return redirect()->back()->with(
            'success',
            ($result['requested'] ?? 0).' school request(s) sent for admin approval.'
        );
    }

    /** Admin: pending school requests. */
    public function adminIndex()
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $requests = SchoolRequestService::allForSession(StateService::scopeStateId());
        $schoolIds = $requests->pluck('school_name')->filter()->unique()->all();
        $schools = School::whereIn('id', $schoolIds)->get()->keyBy('id');

        return view('admin.school-requests', compact('requests', 'schools'));
    }

    public function approve(Request $request, $id)
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $result = SchoolRequestService::approve((int) $id, (int) Auth::id(), $request->input('note'));

        if (!empty($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        return redirect()->back()->with('success', $result['message'] ?? 'Approved.');
    }

    public function reject(Request $request, $id)
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $result = SchoolRequestService::reject((int) $id, (int) Auth::id(), $request->input('note'));

        if (!empty($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        return redirect()->back()->with('success', $result['message'] ?? 'Rejected.');
    }

    /** Trainer: download authorization letter for an approved request. */
    public function downloadAuthLetter($id)
    {
        $user = Auth::user();
        if (!$user || !in_array((int) $user->role, [0, 2], true)) {
            abort(403);
        }

        $row = AsignedSchool::withoutGlobalScopes()
            ->where('id', (int) $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (($row->approval_status ?? '') !== 'approved' || empty($row->auth_letter_path)) {
            abort(404, 'Authorization letter not available.');
        }

        $absolute = \Illuminate\Support\Facades\Storage::disk('public')->path($row->auth_letter_path);
        if (!is_file($absolute)) {
            abort(404, 'Authorization letter file missing.');
        }

        $school = School::find($row->school_name);
        $filename = 'SOPL_Auth_'.preg_replace('/[^A-Za-z0-9_-]+/', '_', $school->school_name ?? 'school').'.pdf';

        return response()->download($absolute, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
