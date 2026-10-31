<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conference extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conference';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'conference_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'chairman_name',
        'chairman_email',
        'conference_phone',
        'conference_email',
        'conference_website',
        'secretariat_address',
        'conference_date',
        'publication',
        'conference_venue',
        'contents',
        'conference_status',
        'organizer_participant_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'conference_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Attribute Aliases / Accessors & Mutators
    |--------------------------------------------------------------------------
    | These accessors provide intuitive aliases matching common controller
    | and view naming conventions (e.g. title -> name, status -> conference_status).
    */

    public function getTitleAttribute(): ?string
    {
        return $this->name;
    }

    public function setTitleAttribute(?string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getStatusAttribute(): ?string
    {
        return $this->conference_status;
    }

    public function setStatusAttribute(?string $value): void
    {
        $this->attributes['conference_status'] = $value;
    }

    public function getAboutAttribute(): ?string
    {
        return $this->contents;
    }

    public function setAboutAttribute(?string $value): void
    {
        $this->attributes['contents'] = $value;
    }

    public function getThemeAttribute(): ?string
    {
        return $this->contents;
    }

    public function setThemeAttribute(?string $value): void
    {
        $this->attributes['contents'] = $value;
    }

    public function getChairpersonAttribute(): ?string
    {
        return $this->chairman_name;
    }

    public function setChairpersonAttribute(?string $value): void
    {
        $this->attributes['chairman_name'] = $value;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        return asset($this->logo);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->conference_status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->conference_status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->conference_status === 'closed';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('conference_status', 'active');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('conference_status', 'draft');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('conference_status', 'closed');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Admins assigned to this conference through the pivot table.
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(
            ConferenceAdmin::class,
            'conference_admin_assignment',
            'conference_id',
            'admin_id'
        )
        ->withPivot('assignment_id', 'assigned_date', 'access_level')
        ->withTimestamps();
    }

    /**
     * Direct admin assignments for this conference.
     */
    public function adminAssignments(): HasMany
    {
        return $this->hasMany(ConferenceAdminAssignment::class, 'conference_id', 'conference_id');
    }

    /**
     * Academic tracks associated with this conference.
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class, 'conference_id', 'conference_id');
    }

    /**
     * Important submission and payment deadlines for this conference.
     */
    public function deadline(): HasOne
    {
        return $this->hasOne(Deadline::class, 'conference_id', 'conference_id');
    }

    /**
     * Registration fee packages/tiers for this conference.
     */
    public function fees(): HasMany
    {
        return $this->hasMany(ConferenceFee::class, 'conference_id', 'conference_id');
    }

    /**
     * Attendee / Author registrations for this conference.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(ConferenceRegistration::class, 'conference_id', 'conference_id');
    }

    /**
     * Reviewer assignments for this conference.
     */
    public function reviewerAssignments(): HasMany
    {
        return $this->hasMany(ConferenceReviewerAssignment::class, 'conference_id', 'conference_id');
    }

    /**
     * Papers submitted to this conference.
     */
    public function papers(): HasMany
    {
        return $this->hasMany(Paper::class, 'conference_id', 'conference_id');
    }
}

/*
|--------------------------------------------------------------------------
| Inline Fallback for ConferenceAdmin
|--------------------------------------------------------------------------
| Defined safely to prevent class re-declaration when standalone model exists.
*/
if (!class_exists(ConferenceAdmin::class)) {
    class ConferenceAdmin extends Model
    {
        protected $table = 'conference_admin';
        protected $primaryKey = 'admin_id';
        protected $fillable = ['name', 'email', 'password'];
        protected $hidden = ['password'];
    }
}
