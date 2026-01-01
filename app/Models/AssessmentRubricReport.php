<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentRubricReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'input_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_by',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Rating level constants.
     */
    public const RATING_LEVELS = [
        1 => ['label' => 'Aware', 'color' => 'red'],
        2 => ['label' => 'Limited', 'color' => 'orange'],
        3 => ['label' => 'Fair', 'color' => 'yellow'],
        4 => ['label' => 'Good', 'color' => 'blue'],
        5 => ['label' => 'Excellent', 'color' => 'green'],
    ];

    /**
     * Input type constants.
     */
    public const INPUT_TYPE_MANUAL = 'manual';

    public const INPUT_TYPE_FILE = 'file';

    /**
     * Get the assessment this rubric report belongs to.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the user who created this rubric report.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who uploaded the file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get all elements for this rubric report.
     */
    public function elements(): HasMany
    {
        return $this->hasMany(AssessmentRubricReportElement::class, 'rubric_report_id')->orderBy('order');
    }

    /**
     * Check if this is a manual input type.
     */
    public function isManualInput(): bool
    {
        return $this->input_type === self::INPUT_TYPE_MANUAL;
    }

    /**
     * Check if this is a file upload type.
     */
    public function isFileUpload(): bool
    {
        return $this->input_type === self::INPUT_TYPE_FILE;
    }

    /**
     * Get rating level labels.
     */
    public static function getRatingLabels(): array
    {
        return array_column(self::RATING_LEVELS, 'label');
    }

    /**
     * Get rating level by number.
     */
    public static function getRatingLevel(int $level): ?array
    {
        return self::RATING_LEVELS[$level] ?? null;
    }

    /**
     * Scope to get only active rubric reports.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
