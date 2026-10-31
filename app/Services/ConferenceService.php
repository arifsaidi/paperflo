<?php

namespace App\Services;

use App\Models\Conference;
use App\Models\ConferenceRegistration;
use App\Models\Deadline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ConferenceService
{
    /**
     * Retrieve a conference by ID.
     */
    public function getConference(int $conferenceId): ?Conference
    {
        return Conference::find($conferenceId);
    }

    /**
     * Retrieve a conference by ID or throw 404.
     */
    public function findConferenceOrFail(int $conferenceId): Conference
    {
        return Conference::findOrFail($conferenceId);
    }

    /**
     * Retrieve all active conferences ordered by conference date.
     */
    public function getActiveConferences(): Collection
    {
        return Conference::active()
            ->orderBy('conference_date', 'asc')
            ->get();
    }

    /**
     * Get conference detail and related information for participant display.
     */
    public function getConferenceDetail(int $conferenceId, ?int $participantId = null): array
    {
        $conference = Conference::active()->where('conference_id', $conferenceId)->first();

        if (!$conference) {
            abort(404, 'Conference not found or not available.');
        }

        $registration = null;
        if ($participantId) {
            $registration = ConferenceRegistration::where('conference_id', $conferenceId)
                ->where('participant_id', $participantId)
                ->first();
        }

        $fees = $conference->fees()->get();
        $tracks = $conference->tracks()->get();
        $deadlines = $conference->deadline()->first();

        return [
            'conference' => $conference,
            'registration' => $registration,
            'fees' => $fees,
            'tracks' => $tracks,
            'deadlines' => $deadlines,
        ];
    }

    /**
     * Update conference primary details and logo.
     */
    public function updateConferenceInfo(int $conferenceId, array $data, ?UploadedFile $logoFile = null): Conference
    {
        $conference = $this->findConferenceOrFail($conferenceId);

        if ($conference->isClosed()) {
            throw new InvalidArgumentException('Conference is closed and cannot be edited.');
        }

        // Handle logo upload if provided
        if ($logoFile) {
            $logoName = 'conference_logo_' . $conference_id . '_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('uploads/logos'), $logoName);
            $data['logo'] = 'uploads/logos/' . $logoName;
        }

        // Filter editable fields based on status
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

        if ($conference->isDraft()) {
            $editableFields[] = 'conference_date';
        }

        if (isset($data['logo'])) {
            $editableFields[] = 'logo';
        }

        $filteredData = array_intersect_key($data, array_flip($editableFields));

        $conference->update($filteredData);

        return $conference;
    }

    /**
     * Update conference description / contents.
     */
    public function updateContents(int $conferenceId, ?string $contents): Conference
    {
        $conference = $this->findConferenceOrFail($conferenceId);

        if ($conference->isClosed()) {
            throw new InvalidArgumentException('Conference is closed and cannot be edited.');
        }

        $conference->update([
            'contents' => $contents,
        ]);

        return $conference;
    }

    /**
     * Activate a draft conference.
     */
    public function activateConference(int $conferenceId): Conference
    {
        return DB::transaction(function () use ($conferenceId) {
            $conference = Conference::where('conference_id', $conferenceId)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$conference->isDraft()) {
                throw new InvalidArgumentException('Only draft conferences can be activated.');
            }

            // Required fields validation
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
                throw new InvalidArgumentException('Missing required fields: ' . implode(', ', $missing));
            }

            $conference->update([
                'conference_status' => 'active',
            ]);

            return $conference;
        });
    }
}
