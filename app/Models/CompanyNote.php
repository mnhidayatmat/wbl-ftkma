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
}
