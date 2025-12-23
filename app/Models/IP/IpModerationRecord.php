<?php

namespace App\Models\IP;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpModerationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'original_lecturer_score',
        'original_ic_score',
        'original_final_score',
        'adjusted_lecturer_score',
        'adjusted_ic_score',
        'adjusted_final_score',
        'adjustment_percentage',
        'adjustment_type',
        'justification',
        'notes',
        'moderated_by',
    ];

    protected $casts = [
        'original_lecturer_score' => 'decimal:2',
        'original_ic_score' => 'decimal:2',
        'original_final_score' => 'decimal:2',
        'adjusted_lecturer_score' => 'decimal:2',
        'adjusted_ic_score' => 'decimal:2',
        'adjusted_final_score' => 'decimal:2',
        'adjustment_percentage' => 'decimal:2',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the moderator.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
