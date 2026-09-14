<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WorkerDocument extends Model
{
    protected $fillable = [
        'worker_id',
        'document_type',
        'label',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    protected $casts = [
        'document_type' => DocumentType::class,
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    /**
     * Generate a temporary signed URL for this document from R2.
     * Default expiry kept short (5 min) since these are sensitive documents
     * (passport, iqama, etc.) — generate fresh on each request, don't cache/store.
     */
    public function temporaryUrl(int $minutes = 5): string
    {
        return Storage::disk('r2')->temporaryUrl(
            $this->file_path,
            now()->addMinutes($minutes)
        );
    }
}