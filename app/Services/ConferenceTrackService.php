<?php

namespace App\Services;

use App\Models\Conference;
use App\Models\Paper;
use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class ConferenceTrackService
{
    /**
     * Retrieve all tracks for a specific conference.
     */
    public function getTracks(int $conferenceId): Collection
    {
        return Track::where('conference_id', $conferenceId)
            ->orderBy('track_id')
            ->get();
    }

    /**
     * Add a new track to a conference.
     */
    public function addTrack(int $conferenceId, string $name): Track
    {
        return Track::create([
            'conference_id' => $conferenceId,
            'name' => $name,
        ]);
    }

    /**
     * Delete a track with safety dependency check.
     */
    public function deleteTrack(int $conferenceId, int $trackId): array
    {
        $track = Track::where('conference_id', $conferenceId)
            ->where('track_id', $trackId)
            ->first();

        if (!$track) {
            abort(404, 'Track not found.');
        }

        // Safety check: is track used by papers?
        $isUsed = Paper::where('track_id', $trackId)->exists();

        if ($isUsed) {
            return [
                'success' => false,
                'message' => 'Cannot delete track as it is currently being used by papers.',
            ];
        }

        $track->delete();

        return [
            'success' => true,
            'message' => 'Track removed successfully.',
        ];
    }
}
