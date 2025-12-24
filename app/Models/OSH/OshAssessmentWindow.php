<?php

namespace App\Models\OSH;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OshAssessmentWindow extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluator_role',
        'is_enabled',
        'start_at',
        'end_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

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
     * Get the current status of the assessment window.
     */
    public function getStatusAttribute(): string
    {
        if (! $this->is_enabled) {
            return 'disabled';
        }

        $now = now();

        if ($this->start_at && $now < $this->start_at) {
            return 'upcoming';
        }

        if ($this->end_at && $now > $this->end_at) {
            return 'closed';
        }

        if ($this->start_at && $this->end_at && $now >= $this->start_at && $now <= $this->end_at) {
            return 'open';
        }

        // If no dates set, consider it open if enabled
        return $this->is_enabled ? 'open' : 'closed';
    }

    /**
     * Check if the window is currently open for evaluation.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Open',
            'closed' => 'Closed',
            'upcoming' => 'Upcoming',
            'disabled' => 'Disabled',
            default => 'Unknown',
        };
    }

    /**
     * Get the assessments associated with this window.
     */
    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(
            Assessment::class,
            'osh_assessment_window_assessments',
            'osh_assessment_window_id',
            'assessment_id'
        )->withTimestamps();
    }

    /**
     * Check if this window applies to a specific assessment.
     *
     * @param  int|null  $assessmentId  The assessment ID to check
     * @return bool
     */
    public function appliesTo(?int $assessmentId = null): bool
    {
        if ($assessmentId === null) {
            return true;
        }

        if ($this->assessments()->count() === 0) {
            return true;
        }

        return $this->assessments()->where('assessment_id', $assessmentId)->exists();
    }

    /**
     * Check if the window is currently open for a specific assessment.
     *
     * @param  int|null  $assessmentId  The assessment ID to check
     * @return bool
     */
    public function isOpenFor(?int $assessmentId = null): bool
    {
        return $this->isOpen() && $this->appliesTo($assessmentId);
    }

    /**
     * Get display text for selected assessments.
     *
     * @return string
     */
    public function getSelectedAssessmentsDisplayAttribute(): string
    {
        $count = $this->assessments()->count();

        if ($count === 0) {
            return 'All assessments';
        }

        if ($count === 1) {
            return $this->assessments->first()->assessment_name;
        }

        return "{$count} assessments selected";
    }
}
