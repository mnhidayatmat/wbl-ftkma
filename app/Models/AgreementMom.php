<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AgreementMom extends Model
{
    use HasFactory;

    protected $table = 'agreement_moms';

    protected $fillable = [
        'title',
        'meeting_date',
        'document_path',
        'document_name',
        'remarks',
        'uploaded_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    /**
     * Get the companies mentioned in this MoM.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'agreement_mom_company')
            ->withTimestamps();
    }

    /**
     * Get the user who uploaded the MoM.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get companies count attribute.
     */
    public function getCompaniesCountAttribute(): int
    {
        return $this->companies()->count();
    }

    /**
     * Get the document URL.
     */
    public function getDocumentUrlAttribute(): ?string
    {
        if (! $this->document_path) {
            return null;
        }

        return \Storage::url($this->document_path);
    }
}
