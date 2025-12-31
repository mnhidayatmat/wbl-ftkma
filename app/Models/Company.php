<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'category',
        'pic_name',
        'position',
        'email',
        'phone',
        'industry_type',
        'address',
        'website',
        'staff_pic_name',
        'staff_pic_phone',
        'ic_name',
        'ic_phone',
        'ic_email',
        'ic_position',
        // MoU Template Fields
        'mou_company_number',
        'mou_company_shortname',
        'mou_signed_behalf_name',
        'mou_signed_behalf_position',
        'mou_witness_name',
        'mou_witness_position',
        'mou_liaison_officer',
        'mou_vc_name',
        'mou_dvc_name',
        'mou_generated_path',
        'mou_generated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mou_generated_at' => 'datetime',
    ];

    /**
     * Get the students for the company.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'company_id');
    }

    /**
     * Get the contacts for the company.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(CompanyContact::class);
    }

    /**
     * Get the primary contact for the company.
     */
    public function primaryContact()
    {
        return $this->hasOne(CompanyContact::class)->where('is_primary', true);
    }

    /**
     * Get the notes for the company.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(CompanyNote::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the documents for the company.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(CompanyDocument::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the MoU for the company.
     */
    public function mou()
    {
        return $this->hasOne(Mou::class);
    }

    /**
     * Get the MoAs for the company.
     */
    public function moas(): HasMany
    {
        return $this->hasMany(Moa::class);
    }

    /**
     * Get all agreements for the company (unified MoU/MoA/LOI).
     */
    public function agreements(): HasMany
    {
        return $this->hasMany(CompanyAgreement::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get active agreements for the company.
     */
    public function activeAgreements(): HasMany
    {
        return $this->hasMany(CompanyAgreement::class)->where('status', 'Active');
    }

    /**
     * Check if company has any active agreement.
     */
    public function hasActiveAgreement(): bool
    {
        return $this->activeAgreements()->exists();
    }

    /**
     * Check if company has active MoU.
     */
    public function hasActiveMou(): bool
    {
        return $this->agreements()->where('agreement_type', 'MoU')->where('status', 'Active')->exists();
    }

    /**
     * Check if company has active MoA.
     */
    public function hasActiveMoa(): bool
    {
        return $this->agreements()->where('agreement_type', 'MoA')->where('status', 'Active')->exists();
    }

    /**
     * Get active students count.
     */
    public function getActiveStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }

    /**
     * Check if MoU is expired.
     */
    public function isMouExpired(): bool
    {
        if (! $this->mou || ! $this->mou->end_date) {
            return false;
        }

        return $this->mou->end_date < now();
    }

    /**
     * Get Industry Coaches (IC) from this company.
     */
    public function industryCoaches(): HasMany
    {
        return $this->hasMany(User::class, 'company_id')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'ic');
            });
    }

    /**
     * Get count of Industry Coaches from this company.
     */
    public function getIndustryCoachesCountAttribute(): int
    {
        return $this->industryCoaches()->count();
    }
}
