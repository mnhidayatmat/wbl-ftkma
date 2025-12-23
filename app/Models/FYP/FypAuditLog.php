<?php

namespace App\Models\FYP;

use App\Models\Assessment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FypAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'action_type',
        'user_id',
        'user_role',
        'student_id',
        'assessment_id',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student (if applicable).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the assessment (if applicable).
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Create an audit log entry.
     */
    public static function log(string $action, string $actionType, string $description, array $metadata = [], ?int $studentId = null, ?int $assessmentId = null): self
    {
        return self::create([
            'action' => $action,
            'action_type' => $actionType,
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'unknown',
            'student_id' => $studentId,
            'assessment_id' => $assessmentId,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
