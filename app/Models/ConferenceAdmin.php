<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceAdmin extends Model
{
    use HasFactory;

    protected $table = 'conference_admin';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(
            Conference::class,
            'conference_admin_assignment',
            'admin_id',
            'conference_id'
        )
        ->withPivot('assignment_id', 'assigned_date', 'access_level')
        ->withTimestamps();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ConferenceAdminAssignment::class, 'admin_id', 'admin_id');
    }
}
