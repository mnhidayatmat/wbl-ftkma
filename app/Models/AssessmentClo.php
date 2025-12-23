<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentClo extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'clo_code',
        'weight_percentage',
        'order',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
    ];

    /**
     * Get the assessment that owns this CLO.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
