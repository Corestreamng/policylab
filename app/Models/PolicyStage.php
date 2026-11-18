<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PolicyStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'sequence_order',
        'name',
        'slug',
        'color_hex',
        'text_color_hex',
        'bg_color_hex',
        'icon_class',
        'description',
        'is_active',
    ];

    protected $casts = [
        'sequence_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function policyStatuses(): HasMany
    {
        return $this->hasMany(PolicyStatus::class);
    }
}
