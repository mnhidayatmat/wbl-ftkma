<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentRubricReportElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'rubric_report_id',
        'element_name',
        'criteria_keywords',
        'weight_percentage',
        'order',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'order' => 'integer',
    ];

    /**
     * Get the rubric report this element belongs to.
     */
    public function rubricReport(): BelongsTo
    {
        return $this->belongsTo(AssessmentRubricReport::class, 'rubric_report_id');
    }

    /**
     * Get all descriptors for this element.
     */
    public function descriptors(): HasMany
    {
        return $this->hasMany(AssessmentRubricReportDescriptor::class, 'element_id')->orderBy('level');
    }

    /**
     * Get descriptor by level.
     */
    public function getDescriptorByLevel(int $level): ?AssessmentRubricReportDescriptor
    {
        return $this->descriptors->where('level', $level)->first();
    }

    /**
     * Scope to order elements.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
