<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CloPloMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'clo_code',
        'clo_description',
        'is_active',
        'allow_for_assessment',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_for_assessment' => 'boolean',
    ];

    /**
     * Get the PLO relationships for this CLO.
     */
    public function ploRelationships(): HasMany
    {
        return $this->hasMany(CloPloRelationship::class, 'clo_plo_mapping_id');
    }

    /**
     * Get the PLO codes as an array.
     */
    public function getPloCodesAttribute(): array
    {
        return $this->ploRelationships()->pluck('plo_code')->toArray();
    }

    /**
     * Get the creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater user.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get active CLOs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get CLOs allowed for assessment.
     */
    public function scopeAllowedForAssessment($query)
    {
        // Check if clo_plo_relationships table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('clo_plo_relationships')) {
            // If table doesn't exist, return empty query (will fallback to default CLOs)
            return $query->whereRaw('1 = 0');
        }
        
        return $query->where('is_active', true)
            ->where('allow_for_assessment', true)
            ->whereHas('ploRelationships'); // Must have at least one PLO mapping
    }

    /**
     * Scope to filter by course.
     */
    public function scopeForCourse($query, string $courseCode)
    {
        return $query->where('course_code', $courseCode);
    }

    /**
     * Check if CLO is eligible for assessment.
     */
    public function isEligibleForAssessment(): bool
    {
        return $this->is_active 
            && $this->allow_for_assessment 
            && $this->ploRelationships()->exists();
    }
}
