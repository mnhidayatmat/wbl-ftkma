<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentRubricReportDescriptor extends Model
{
    use HasFactory;

    protected $fillable = [
        'element_id',
        'level',
        'label',
        'descriptor',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Get the element this descriptor belongs to.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(AssessmentRubricReportElement::class, 'element_id');
    }

    /**
     * Get the color for this level.
     */
    public function getColor(): string
    {
        $levels = AssessmentRubricReport::RATING_LEVELS;

        return $levels[$this->level]['color'] ?? 'gray';
    }

    /**
     * Scope to order by level.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level');
    }
}
