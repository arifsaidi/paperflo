<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConferenceDeadlineController extends Controller
{
    public function index($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        $deadline = DB::table('deadline')
            ->where('conference_id', $conference_id)
            ->first();

        // Initialize if not exists
        if (!$deadline) {
            $defaultDate = now()->addMonths(3)->format('Y-m-d H:i:s');
            $deadlineId = DB::table('deadline')->insertGetId([
                'conference_id' => $conference_id,
                'abstract_deadline' => $defaultDate,
                'fullpaper_deadline' => $defaultDate,
                'cameraready_paper_deadline' => $defaultDate,
                'participant_registration_deadline' => $defaultDate,
                'review_deadline' => $defaultDate,
                'listener_payment_deadline' => $defaultDate,
                'presenter_payment_deadline' => $defaultDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $deadline = DB::table('deadline')->where('deadline_id', $deadlineId)->first();
        }

        return view('conference.deadlines.index', compact('conference', 'deadline'));
    }

    public function update(Request $request, $conference_id)
    {
        $request->validate([
            'abstract_deadline' => 'required|date',
            'fullpaper_deadline' => 'required|date',
            'cameraready_paper_deadline' => 'required|date',
            'participant_registration_deadline' => 'required|date',
            'review_deadline' => 'required|date',
            'listener_payment_deadline' => 'required|date',
            'presenter_payment_deadline' => 'required|date',
        ]);

        DB::table('deadline')
            ->where('conference_id', $conference_id)
            ->update([
                'abstract_deadline' => $request->abstract_deadline,
                'fullpaper_deadline' => $request->fullpaper_deadline,
                'cameraready_paper_deadline' => $request->cameraready_paper_deadline,
                'participant_registration_deadline' => $request->participant_registration_deadline,
                'review_deadline' => $request->review_deadline,
                'listener_payment_deadline' => $request->listener_payment_deadline,
                'presenter_payment_deadline' => $request->presenter_payment_deadline,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Deadlines updated successfully.');
    }
}
