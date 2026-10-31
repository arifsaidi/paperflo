<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    use HasFactory;

    protected $table = 'deadline';
    protected $primaryKey = 'deadline_id';

    protected $fillable = [
        'abstract_deadline',
        'fullpaper_deadline',
        'cameraready_paper_deadline',
        'participant_registration_deadline',
        'review_deadline',
        'listener_payment_deadline',
        'presenter_payment_deadline',
        'conference_id',
    ];

    protected function casts(): array
    {
        return [
            'abstract_deadline' => 'datetime',
            'fullpaper_deadline' => 'datetime',
            'cameraready_paper_deadline' => 'datetime',
            'participant_registration_deadline' => 'datetime',
            'review_deadline' => 'datetime',
            'listener_payment_deadline' => 'datetime',
            'presenter_payment_deadline' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }
}
