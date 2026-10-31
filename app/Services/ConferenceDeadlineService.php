<?php

namespace App\Services;

use App\Models\Deadline;

class ConferenceDeadlineService
{
    /**
     * Get or initialize default deadlines for a conference.
     */
    public function getOrCreateDeadlines(int $conferenceId): Deadline
    {
        $deadline = Deadline::where('conference_id', $conferenceId)->first();

        if (!$deadline) {
            $defaultDate = now()->addMonths(3);

            $deadline = Deadline::create([
                'conference_id' => $conferenceId,
                'abstract_deadline' => $defaultDate,
                'fullpaper_deadline' => $defaultDate,
                'cameraready_paper_deadline' => $defaultDate,
                'participant_registration_deadline' => $defaultDate,
                'review_deadline' => $defaultDate,
                'listener_payment_deadline' => $defaultDate,
                'presenter_payment_deadline' => $defaultDate,
            ]);
        }

        return $deadline;
    }

    /**
     * Update deadlines for a conference.
     */
    public function updateDeadlines(int $conferenceId, array $data): Deadline
    {
        $deadline = Deadline::where('conference_id', $conferenceId)->first();

        if (!$deadline) {
            $deadline = new Deadline(['conference_id' => $conferenceId]);
        }

        $deadline->fill([
            'abstract_deadline' => $data['abstract_deadline'],
            'fullpaper_deadline' => $data['fullpaper_deadline'],
            'cameraready_paper_deadline' => $data['cameraready_paper_deadline'],
            'participant_registration_deadline' => $data['participant_registration_deadline'],
            'review_deadline' => $data['review_deadline'],
            'listener_payment_deadline' => $data['listener_payment_deadline'],
            'presenter_payment_deadline' => $data['presenter_payment_deadline'],
        ]);

        $deadline->save();

        return $deadline;
    }
}
