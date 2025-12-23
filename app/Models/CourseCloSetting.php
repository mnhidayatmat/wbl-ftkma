<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCloSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'clo_count',
        'updated_by',
    ];

    protected $casts = [
        'clo_count' => 'integer',
    ];

    /**
     * Get the user who last updated this setting.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get CLO count for a course.
     */
    public static function getCloCount(string $courseCode): int
    {
        $setting = static::where('course_code', $courseCode)->first();

        if ($setting) {
            return $setting->clo_count;
        }

        // Default CLO counts if not set
        $defaults = [
            'PPE' => 4,
            'IP' => 4,
            'OSH' => 4,
            'FYP' => 7,
            'LI' => 4,
        ];

        return $defaults[$courseCode] ?? 4;
    }

    /**
     * Generate CLO codes for a course.
     */
    public static function getCloCodes(string $courseCode): array
    {
        $count = static::getCloCount($courseCode);
        $clos = [];

        for ($i = 1; $i <= $count; $i++) {
            $clos[] = 'CLO'.$i;
        }

        return $clos;
    }

    /**
     * Update CLO count for a course.
     */
    public static function updateCloCount(string $courseCode, int $count, ?int $userId = null): self
    {
        return static::updateOrCreate(
            ['course_code' => $courseCode],
            [
                'clo_count' => $count,
                'updated_by' => $userId,
            ]
        );
    }
}
