<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paper extends Model
{
    use HasFactory;

    protected $table = 'paper';
    protected $primaryKey = 'paper_id';

    protected $fillable = [
        'title',
        'conference_id',
        'track_id',
        'keywords',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class, 'track_id', 'track_id');
    }
}
