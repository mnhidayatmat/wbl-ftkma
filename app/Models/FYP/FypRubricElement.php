<?php

namespace App\Models\FYP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FypRubricElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'rubric_template_id',
        'element_code',
        'name',
        'description',
        'clo_code',
        'weight_percentage',
        'contribution_to_grade',
        'order',
        'is_active',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'contribution_to_grade' => 'decimal:2',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the rubric template this element belongs to.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(FypRubricTemplate::class, 'rubric_template_id');
    }

    /**
     * Get all level descriptors for this element.
     */
    public function levelDescriptors(): HasMany
    {
        return $this->hasMany(FypRubricLevelDescriptor::class, 'rubric_element_id')->orderBy('level');
    }

    /**
     * Get all evaluations for this element.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(FypRubricEvaluation::class, 'rubric_element_id');
    }

    /**
     * Scope to filter active elements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get descriptor for a specific level.
     */
    public function getDescriptorForLevel(int $level): ?FypRubricLevelDescriptor
    {
        return $this->levelDescriptors()->where('level', $level)->first();
    }

    /**
     * Calculate weighted score for a given level.
     */
    public function calculateWeightedScore(int $level): float
    {
        $descriptor = $this->getDescriptorForLevel($level);
        if (! $descriptor) {
            return 0;
        }

        // Score = (level_score / max_level_score) * element_weight
        $maxScore = 5; // Fixed max level

        return ($descriptor->score_value / $maxScore) * $this->weight_percentage;
    }

    /**
     * Check if this element has all 5 level descriptors.
     */
    public function hasAllDescriptors(): bool
    {
        return $this->levelDescriptors()->count() === 5;
    }

    /**
     * Create default level descriptors for this element.
     */
    public function createDefaultDescriptors(): void
    {
        $defaultLabels = FypRubricTemplate::PERFORMANCE_LEVELS;

        foreach ($defaultLabels as $level => $label) {
            FypRubricLevelDescriptor::firstOrCreate(
                [
                    'rubric_element_id' => $this->id,
                    'level' => $level,
                ],
                [
                    'label' => $label,
                    'descriptor' => "Performance level {$level} - {$label}",
                    'score_value' => $level,
                ]
            );
        }
    }
}
