<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'policy_stage_id',
        'is_stalled',
        'is_expired',
        'stalled_reason',
        'expired_reason',
        'year_commenced',
        'data_verified_at',
        'source_notes',
        'objectives_summary',
    ];

    protected $casts = [
        'is_stalled' => 'boolean',
        'is_expired' => 'boolean',
        'data_verified_at' => 'date',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PolicyStage::class, 'policy_stage_id');
    }
}
