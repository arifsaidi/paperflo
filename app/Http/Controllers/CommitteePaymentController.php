<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommitteePaymentController extends Controller
{
    /**
     * List all payments (presenter & listener) for a specific conference.
     */
    public function index($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        // Fetch Presenter Payments (linked to papers and presenters)
        $presenterPayments = DB::table('presenter_payment')
            ->join('presenter_assignment', 'presenter_payment.presenter_assignment_id', '=', 'presenter_assignment.presenter_assignment_id')
            ->join('participant', 'presenter_assignment.participant_id', '=', 'participant.participant_id')
            ->join('paper', 'presenter_assignment.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->select(
                'presenter_payment.amount',
                'presenter_payment.status',
                'presenter_payment.paid_date as date',
                'participant.name as payer_name',
                'participant.email as payer_email',
                DB::raw("'Presenter' as payment_type"),
                'paper.title as reference'
            )
            ->get();

        // Fetch Listener Payments (linked directly to conference registration)
        $listenerPayments = DB::table('listener_payment')
            ->join('conference_registration', 'listener_payment.registration_id', '=', 'conference_registration.registration_id')
            ->join('participant', 'conference_registration.participant_id', '=', 'participant.participant_id')
            ->where('conference_registration.conference_id', $conference_id)
            ->select(
                'listener_payment.amount',
                'listener_payment.payment_status as status',
                'listener_payment.created_at as date', // Listener payment table doesn't have paid_date, using created_at
                'participant.name as payer_name',
                'participant.email as payer_email',
                DB::raw("'Listener' as payment_type"),
                DB::raw("'-' as reference")
            )
            ->get();

        return view('committee.payments.index', compact('conference', 'presenterPayments', 'listenerPayments'));
    }
}
