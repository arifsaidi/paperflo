<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceAdminAssignment extends Model
{
    use HasFactory;

    protected $table = 'conference_admin_assignment';
    protected $primaryKey = 'assignment_id';

    protected $fillable = [
        'assigned_date',
        'access_level',
        'admin_id',
        'conference_id',
    ];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(ConferenceAdmin::class, 'admin_id', 'admin_id');
    }
}
