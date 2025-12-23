<?php

namespace App\Models\FYP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FypRubricLevelDescriptor extends Model
{
    use HasFactory;

    protected $fillable = [
        'rubric_element_id',
        'level',
        'label',
        'descriptor',
        'score_value',
    ];

    protected $casts = [
        'level' => 'integer',
        'score_value' => 'decimal:2',
    ];

    /**
     * Get the rubric element this descriptor belongs to.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(FypRubricElement::class, 'rubric_element_id');
    }

    /**
     * Scope to order by level.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level');
    }

    /**
     * Get the level label with score.
     */
    public function getLabelWithScoreAttribute(): string
    {
        return "{$this->label} ({$this->score_value})";
    }

    /**
     * Get CSS class for level badge based on level number.
     */
    public function getLevelColorClassAttribute(): string
    {
        return match ($this->level) {
            1 => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            2 => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
            3 => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            4 => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            5 => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }
}
