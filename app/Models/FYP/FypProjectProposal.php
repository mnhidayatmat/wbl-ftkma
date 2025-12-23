<?php

namespace App\Models\FYP;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FypProjectProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'project_title',
        'proposal_items',
        'status',
        'remarks',
        'approved_by',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'proposal_items' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the student that owns the proposal.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who approved the proposal.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if proposal is editable.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * Check if proposal can be submitted.
     */
    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft' && ! empty($this->project_title) && ! empty($this->proposal_items);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'submitted' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Needs Revision',
            default => 'Unknown',
        };
    }
}
