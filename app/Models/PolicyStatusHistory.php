<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'policy_stage_id',
        'transitioned_date',
        'change_notes',
        'created_by',
    ];

    protected $casts = [
        'transitioned_date' => 'date',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PolicyStage::class, 'policy_stage_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
