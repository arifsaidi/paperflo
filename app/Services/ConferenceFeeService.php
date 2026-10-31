<?php

namespace App\Services;

use App\Models\ConferenceFee;
use Illuminate\Database\Eloquent\Collection;

class ConferenceFeeService
{
    /**
     * Retrieve all fee packages for a conference.
     */
    public function getFees(int $conferenceId): Collection
    {
        return ConferenceFee::where('conference_id', $conferenceId)
            ->orderBy('fee_id')
            ->get();
    }

    /**
     * Sync and update fee tiers for a conference.
     */
    public function syncFees(
        int $conferenceId,
        ?array $fees = [],
        ?string $newFeeName = null,
        ?float $newFeeAmount = null,
        string $currency = 'MYR'
    ): void {
        // Update existing fees
        if (!empty($fees)) {
            foreach ($fees as $feeId => $feeData) {
                ConferenceFee::where('fee_id', $feeId)
                    ->where('conference_id', $conferenceId)
                    ->update([
                        'name' => $feeData['name'],
                        'amount' => $feeData['amount'],
                    ]);
            }
        }

        // Add new fee if provided
        if (!empty($newFeeName) && !is_null($newFeeAmount)) {
            ConferenceFee::create([
                'conference_id' => $conferenceId,
                'name' => $newFeeName,
                'amount' => $newFeeAmount,
                'currency' => $currency,
            ]);
        }
    }

    /**
     * Delete a fee category.
     */
    public function deleteFee(int $conferenceId, int $feeId): bool
    {
        $fee = ConferenceFee::where('conference_id', $conferenceId)
            ->where('fee_id', $feeId)
            ->first();

        if (!$fee) {
            abort(404, 'Fee category not found.');
        }

        return (bool) $fee->delete();
    }
}
