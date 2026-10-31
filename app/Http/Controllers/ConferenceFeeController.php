<?php

namespace App\Http\Controllers;

use App\Services\ConferenceFeeService;
use App\Services\ConferenceService;
use Illuminate\Http\Request;

class ConferenceFeeController extends Controller
{
    public function __construct(
        protected ConferenceService $conferenceService,
        protected ConferenceFeeService $feeService
    ) {}

    public function index($conference_id)
    {
        $conference = $this->conferenceService->findConferenceOrFail($conference_id);
        $fees = $this->feeService->getFees($conference_id);

        return view('conference.fees.index', compact('conference', 'fees'));
    }

    public function update(Request $request, $conference_id)
    {
        $request->validate([
            'new_fee_name' => 'nullable|string|max:255',
            'new_fee_amount' => 'nullable|numeric|min:0',
            'fees' => 'nullable|array',
            'fees.*.name' => 'required|string|max:255',
            'fees.*.amount' => 'required|numeric|min:0',
        ]);

        $this->feeService->syncFees(
            $conference_id,
            $request->fees,
            $request->new_fee_name,
            $request->new_fee_amount ? (float) $request->new_fee_amount : null
        );

        return back()->with('success', 'Fees updated successfully.');
    }

    public function destroy($conference_id, $fee_id)
    {
        $this->feeService->deleteFee($conference_id, $fee_id);

        return back()->with('success', 'Fee category removed successfully.');
    }
}
