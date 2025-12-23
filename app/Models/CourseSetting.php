<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_type',
        'lecturer_id',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the lecturer assigned to this course.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get the user who created this setting.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this setting.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get or create course setting for a specific course type.
     */
    public static function getOrCreate(string $courseType): self
    {
        return static::firstOrCreate(
            ['course_type' => $courseType],
            ['created_by' => auth()->id()]
        );
    }
}
