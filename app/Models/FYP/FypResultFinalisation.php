<?php

namespace App\Models\FYP;

use App\Models\Student;
use App\Models\User;
use App\Models\WblGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FypResultFinalisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'group_id',
        'finalisation_scope',
        'is_finalised',
        'notes',
        'finalised_by',
        'finalised_at',
    ];

    protected $casts = [
        'is_finalised' => 'boolean',
        'finalised_at' => 'datetime',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the group.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(WblGroup::class);
    }

    /**
     * Get the user who finalised.
     */
    public function finaliser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalised_by');
    }
}
