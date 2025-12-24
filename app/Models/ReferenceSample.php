<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceSample extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'description',
        'uploaded_by',
        'is_active',
        'display_order',
        'download_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'display_order' => 'integer',
        'download_count' => 'integer',
    ];

    // Relationships
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper Methods
    public function getCategoryDisplayAttribute(): string
    {
        return match ($this->category) {
            'resume' => 'Resume',
            'poster' => 'Poster',
            'achievement' => 'Achievement',
            'other' => 'Other',
            default => 'Unknown',
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'resume' => '📄',
            'poster' => '🖼️',
            'achievement' => '🏆',
            'other' => '📁',
            default => '📄',
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    // Scope queries
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at', 'desc');
    }
}
