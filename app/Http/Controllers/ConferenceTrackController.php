<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConferenceTrackController extends Controller
{
    public function index($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        $tracks = DB::table('track')
            ->where('conference_id', $conference_id)
            ->orderBy('track_id')
            ->get();

        return view('conference.tracks.index', compact('conference', 'tracks'));
    }

    public function store(Request $request, $conference_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::table('track')->insert([
            'conference_id' => $conference_id,
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Track added successfully.');
    }

    public function destroy($conference_id, $track_id)
    {
        // Safety check
        $track = DB::table('track')
            ->where('conference_id', $conference_id)
            ->where('track_id', $track_id)
            ->first();

        if (!$track) {
            abort(404);
        }

        // Check if track is used
        $isUsed = DB::table('paper')
            ->where('track_id', $track_id)
            ->exists();

        if ($isUsed) {
            return back()->with('error', 'Cannot delete track as it is currently being used by papers.');
        }

        DB::table('track')
            ->where('track_id', $track_id)
            ->delete();

        return back()->with('success', 'Track removed successfully.');
    }
}
