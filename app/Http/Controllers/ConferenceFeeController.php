<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConferenceFeeController extends Controller
{
    public function index($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        $fees = DB::table('conference_fees')
            ->where('conference_id', $conference_id)
            ->orderBy('fee_id')
            ->get();

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

        // Update existing fees
        if ($request->has('fees')) {
            foreach ($request->fees as $id => $feeData) {
                DB::table('conference_fees')
                    ->where('fee_id', $id)
                    ->where('conference_id', $conference_id)
                    ->update([
                        'name' => $feeData['name'],
                        'amount' => $feeData['amount'],
                        'updated_at' => now(),
                    ]);
            }
        }

        // Add new fee
        if ($request->filled('new_fee_name') && $request->filled('new_fee_amount')) {
            DB::table('conference_fees')->insert([
                'conference_id' => $conference_id,
                'name' => $request->new_fee_name,
                'amount' => $request->new_fee_amount,
                'currency' => 'MYR',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Fees updated successfully.');
    }

    public function destroy($conference_id, $fee_id)
    {
        $fee = DB::table('conference_fees')
            ->where('conference_id', $conference_id)
            ->where('fee_id', $fee_id)
            ->first();

        if (!$fee) {
            abort(404);
        }

        DB::table('conference_fees')
            ->where('fee_id', $fee_id)
            ->delete();

        return back()->with('success', 'Fee category removed successfully.');
    }
}
