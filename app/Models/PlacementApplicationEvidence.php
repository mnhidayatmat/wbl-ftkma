<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementApplicationEvidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'placement_tracking_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'description',
        'uploaded_by',
    ];

    /**
     * Get the placement tracking.
     */
    public function placementTracking(): BelongsTo
    {
        return $this->belongsTo(StudentPlacementTracking::class, 'placement_tracking_id');
    }

    /**
     * Get the user who uploaded.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}


