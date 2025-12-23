<?php

namespace App\Models\PPE;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpeAssessmentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'clo',
        'weight',
        'max_mark',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'max_mark' => 'decimal:2',
    ];

    /**
     * Get the Lecturer marks for this assessment.
     */
    public function atMarks(): HasMany
    {
        return $this->hasMany(PpeStudentAtMark::class, 'assignment_id');
    }
}

