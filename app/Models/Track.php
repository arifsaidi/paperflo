<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Track extends Model
{
    use HasFactory;

    protected $table = 'track';
    protected $primaryKey = 'track_id';

    protected $fillable = [
        'name',
        'conference_id',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }

    public function papers(): HasMany
    {
        return $this->hasMany(Paper::class, 'track_id', 'track_id');
    }
}
