<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CloPloRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'clo_plo_mapping_id',
        'plo_code',
        'plo_description',
    ];

    /**
     * Get the CLO-PLO mapping this relationship belongs to.
     */
    public function cloPloMapping(): BelongsTo
    {
        return $this->belongsTo(CloPloMapping::class, 'clo_plo_mapping_id');
    }
}
