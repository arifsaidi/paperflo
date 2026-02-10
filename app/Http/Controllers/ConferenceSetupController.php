<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConferenceSetupController extends Controller
{
    public function edit($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404, 'Conference not found');
        }

        return view('conference.setup', compact('conference'));
    }

    public function update(Request $request, $conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        if ($conference->conference_status === 'closed') {
            return back()->with('error', 'Conference is closed and cannot be edited.');
        }

        // Decide editable fields based on status
        $editableFields = [
            'name',
            'chairman_name',
            'chairman_email',
            'conference_phone',
            'conference_email',
            'conference_website',
            'publication',
            'conference_venue',
            'secretariat_address',
        ];

        if ($conference->conference_status === 'draft') {
            $editableFields[] = 'conference_date';
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'chairman_name' => 'required|string|max:255',
            'chairman_email' => 'required|email|max:255',
            'conference_phone' => 'nullable|string|max:20',
            'conference_email' => 'nullable|email|max:255',
            'conference_website' => 'nullable|url|max:255',
            'publication' => 'nullable|string|max:255',
            'conference_venue' => 'required|string|max:255',
            'secretariat_address' => 'nullable|string',
            'conference_date' => 'nullable|date',
        ]);

        $data = $request->only($editableFields);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'conference_logo_' . $conference_id . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/logos'), $logoName);
            $data['logo'] = 'uploads/logos/' . $logoName;
        }

        $data['updated_at'] = now();

        DB::table('conference')
            ->where('conference_id', $conference_id)
            ->update($data);

        return back()->with('success', 'Conference information updated.');
    }

    public function editContents($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        return view('conference.contents', compact('conference'));
    }

    public function updateContents(Request $request, $conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        if ($conference->conference_status === 'closed') {
            return back()->with('error', 'Conference is closed and cannot be edited.');
        }

        $request->validate([
            'contents' => 'nullable|string',
        ]);

        DB::table('conference')
            ->where('conference_id', $conference_id)
            ->update([
                'contents' => $request->contents,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Conference description updated.');
    }

    public function editStatus($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        return view('conference.status', compact('conference'));
    }

    public function activate($conference_id)
    {
        DB::transaction(function () use ($conference_id) {

            $conference = DB::table('conference')
                ->where('conference_id', $conference_id)
                ->lockForUpdate()
                ->first();

            if (!$conference) {
                abort(404);
            }

            if ($conference->conference_status !== 'draft') {
                abort(400, 'Only draft conferences can be activated.');
            }

            // Required before activation
            $required = [
                'chairman_name' => 'Chairman Name',
                'chairman_email' => 'Chairman Email',
                'conference_date' => 'Conference Date',
                'conference_venue' => 'Conference Venue',
            ];

            $missing = [];
            foreach ($required as $field => $label) {
                if (empty($conference->$field)) {
                    $missing[] = $label;
                }
            }

            if (!empty($missing)) {
                abort(400, 'Missing required fields: ' . implode(', ', $missing));
            }

            DB::table('conference')
                ->where('conference_id', $conference_id)
                ->update([
                    'conference_status' => 'active',
                    'updated_at' => now(),
                ]);
        });

        return back()->with('success', 'Conference activated successfully.');
    }
}
