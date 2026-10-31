<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceReviewerAssignment extends Model
{
    use HasFactory;

    protected $table = 'conference_reviewer_assignment';
    protected $primaryKey = 'conf_reviewer_id';

    protected $fillable = [
        'invite_date',
        'invite_status',
        'reviewer_type',
        'reviewer_id',
        'conference_id',
    ];

    protected function casts(): array
    {
        return [
            'invite_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }
}
