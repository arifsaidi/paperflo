<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceRegistration extends Model
{
    use HasFactory;

    protected $table = 'conference_registration';
    protected $primaryKey = 'registration_id';

    protected $fillable = [
        'registration_date',
        'registration_type',
        'registration_category',
        'registration_status',
        'participant_id',
        'conference_id',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }
}
