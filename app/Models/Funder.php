<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'acronym',
        'type',
        'website_url',
    ];

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'region_funder')
            ->withPivot('grant_title')
            ->withTimestamps();
    }
}
