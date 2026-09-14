<?php

namespace App\Models;

use App\Enums\WorkerOrderStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class JobOrderWorker extends Pivot
{
    protected $table = 'job_order_worker';

    public $incrementing = true;

    protected $fillable = [
        'job_order_id',
        'worker_id',
        'status',
        'contacted_at',
        'contacted_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => WorkerOrderStatus::class,
            'contacted_at' => 'datetime',
        ];
    }

    public function contactedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }
}