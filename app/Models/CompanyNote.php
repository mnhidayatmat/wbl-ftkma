<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'note',
        'follow_up_type',
        'next_action_date',
        'action_status',
        'created_by',
    ];

    protected $casts = [
        'next_action_date' => 'date',
    ];

    /**
     * Get the company that owns the note.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the note.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get follow-up type options.
     */
    public static function getFollowUpTypeOptions(): array
    {
        return [
            'Email' => 'Email',
            'Call' => 'Call',
            'Meeting' => 'Meeting',
            'Reminder sent' => 'Reminder sent',
        ];
    }

    /**
     * Get pending actions (next action date is today or in the past, status is pending).
     */
    public static function getPendingActions()
    {
        return self::with(['company', 'creator'])
            ->whereNotNull('next_action_date')
            ->where('action_status', 'pending')
            ->where('next_action_date', '<=', now()->addDays(7)) // Show actions due in next 7 days
            ->orderBy('next_action_date', 'asc')
            ->get();
    }

    /**
     * Get overdue actions.
     */
    public static function getOverdueActions()
    {
        return self::with(['company', 'creator'])
            ->whereNotNull('next_action_date')
            ->where('action_status', 'pending')
            ->where('next_action_date', '<', now())
            ->orderBy('next_action_date', 'asc')
            ->get();
    }

    /**
     * Mark action as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update(['action_status' => 'completed']);
    }

    /**
     * Mark action as dismissed.
     */
    public function markAsDismissed(): void
    {
        $this->update(['action_status' => 'dismissed']);
    }

    /**
     * Check if action is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->next_action_date &&
               $this->next_action_date->isPast() &&
               $this->action_status === 'pending';
    }

    /**
     * Get days until due (negative if overdue).
     */
    public function getDaysUntilDue(): ?int
    {
        if (!$this->next_action_date) {
            return null;
        }

        return now()->diffInDays($this->next_action_date, false);
    }
}
