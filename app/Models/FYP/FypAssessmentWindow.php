<?php

namespace App\Models\FYP;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FypAssessmentWindow extends Model
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
        if (!$this->is_enabled) {
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
        return match($this->status) {
            'open' => 'Open',
            'closed' => 'Closed',
            'upcoming' => 'Upcoming',
            'disabled' => 'Disabled',
            default => 'Unknown',
        };
    }
}


















