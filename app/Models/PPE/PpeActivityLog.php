<?php

namespace App\Models\PPE;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpeActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'evaluator_role',
        'description',
        'admin_user_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the admin user who performed the action.
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get action label for display.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'window_opened' => 'Assessment Window Opened',
            'window_closed' => 'Assessment Window Closed',
            'window_updated' => 'Assessment Window Updated',
            'reminder_sent_lecturer' => 'Reminder Sent (Lecturer)',
            'reminder_sent_ic' => 'Reminder Sent (Industry Coach)',
            'window_enabled' => 'Window Enabled',
            'window_disabled' => 'Window Disabled',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}
