<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'agreement_type',
        'agreement_title',
        'reference_no',
        'start_date',
        'end_date',
        'signed_date',
        'status',
        'faculty',
        'programme',
        'remarks',
        'staff_pic_name',
        'staff_pic_phone',
        'document_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_date' => 'date',
    ];

    /**
     * Boot method to automatically update expired agreements.
     */
    protected static function booted(): void
    {
        // Check and update status when agreement is retrieved from database
        static::retrieved(function (CompanyAgreement $agreement) {
            if ($agreement->status === 'Active' && $agreement->isExpired()) {
                $agreement->updateQuietly(['status' => 'Expired']);
            }
        });

        // Check and update status before saving
        static::saving(function (CompanyAgreement $agreement) {
            if ($agreement->status === 'Active' && $agreement->isExpired()) {
                $agreement->status = 'Expired';
            }
        });
    }

    /**
     * Agreement type options.
     */
    public const AGREEMENT_TYPES = [
        'MoU' => 'Memorandum of Understanding',
        'MoA' => 'Memorandum of Agreement',
        'LOI' => 'Letter of Intent',
    ];

    /**
     * Status options.
     */
    public const STATUS_OPTIONS = [
        'Active' => 'Active',
        'Expired' => 'Expired',
        'Terminated' => 'Terminated',
        'Pending' => 'Pending',
        'Draft' => 'Draft',
    ];

    /**
     * Get the company that owns the agreement.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the agreement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the agreement.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to filter by agreement type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('agreement_type', $type);
    }

    /**
     * Scope to filter active agreements.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope to filter expired agreements.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'Expired');
    }

    /**
     * Scope to filter agreements expiring within given months.
     */
    public function scopeExpiringWithin($query, int $months)
    {
        return $query->where('status', 'Active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addMonths($months)]);
    }

    /**
     * Check if the agreement is expired.
     */
    public function isExpired(): bool
    {
        if (! $this->end_date) {
            return false;
        }

        return $this->end_date->isPast();
    }

    /**
     * Check if the agreement is expiring soon (within 3 months).
     */
    public function isExpiringSoon(): bool
    {
        if (! $this->end_date || $this->status !== 'Active') {
            return false;
        }

        return $this->end_date->isBetween(now(), now()->addMonths(3));
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (! $this->end_date) {
            return null;
        }

        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Get the full agreement type name.
     */
    public function getAgreementTypeFullAttribute(): string
    {
        return self::AGREEMENT_TYPES[$this->agreement_type] ?? $this->agreement_type;
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Active' => 'green',
            'Expired' => 'red',
            'Terminated' => 'gray',
            'Pending' => 'yellow',
            'Draft' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get agreement type badge color.
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->agreement_type) {
            'MoU' => 'blue',
            'MoA' => 'purple',
            'LOI' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Auto-update status based on end_date.
     */
    public function updateStatusIfExpired(): void
    {
        if ($this->status === 'Active' && $this->isExpired()) {
            $this->update(['status' => 'Expired']);
        }
    }

    /**
     * Get summary statistics.
     */
    public static function getSummaryStats(): array
    {
        return [
            'total_mou_active' => self::ofType('MoU')->active()->count(),
            'total_moa_active' => self::ofType('MoA')->active()->count(),
            'total_loi_active' => self::ofType('LOI')->active()->count(),
            'expiring_3_months' => self::expiringWithin(3)->count(),
            'expiring_6_months' => self::expiringWithin(6)->count(),
            'total_active' => self::active()->count(),
            'total_expired' => self::expired()->count(),
        ];
    }
}
