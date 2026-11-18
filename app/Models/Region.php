<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'country_id',
        'name',
        'slug',
        'iso_code',
        'zone',
        'svg_path_d',
        'grid_col',
        'grid_row',
        'center_latitude',
        'center_longitude',
    ];

    protected $casts = [
        'grid_col' => 'integer',
        'grid_row' => 'integer',
        'center_latitude' => 'decimal:7',
        'center_longitude' => 'decimal:7',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function policyStatus(): HasOne
    {
        return $this->hasOne(PolicyStatus::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(PolicyStatusHistory::class)->orderBy('transitioned_date', 'desc');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'region_organization')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function leadingOrganizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'region_organization')
            ->wherePivot('role', 'lead')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function funders(): BelongsToMany
    {
        return $this->belongsToMany(Funder::class, 'region_funder')
            ->withPivot('grant_title')
            ->withTimestamps();
    }

    public function focusAreas(): BelongsToMany
    {
        return $this->belongsToMany(FocusArea::class, 'region_focus_area')
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function publicDocuments(): HasMany
    {
        return $this->hasMany(Document::class)->where('is_public', true);
    }
}
