<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceRequest extends Model
{
    use HasFactory;

    protected $table = 'conference_request';
    protected $primaryKey = 'request_id';

    public $timestamps = false;

    protected $fillable = [
        'conference_title',
        'org_name',
        'org_type',
        'org_address',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'status',
        'submitted_date',
        'participant_id',
        'superadmin_id',
    ];

    protected function casts(): array
    {
        return [
            'submitted_date' => 'datetime',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'Rejected');
    }
}
