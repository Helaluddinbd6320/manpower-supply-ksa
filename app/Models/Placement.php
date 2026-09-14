<?php

namespace App\Models;

use App\Enums\PlacementStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Placement extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'job_order_id',
        'client_company_name',
        'start_date',
        'contract_end_date',
        'client_billing_rate',
        'worker_payout_rate',
        'status',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'contract_end_date' => 'date',
            'client_billing_rate' => 'decimal:2',
            'worker_payout_rate' => 'decimal:2',
            'status' => PlacementStatus::class,
        ];
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Monthly duty/payment records for this placement.
     * Relation added in Step 37 once placement_monthly_records exists.
     */
    // public function monthlyRecords(): HasMany
    // {
    //     return $this->hasMany(PlacementMonthlyRecord::class);
    // }

    /**
     * Monthly margin/profit per worker: Billing Rate − Payout Rate.
     * Computed on the fly, not stored. Returns null if either rate
     * has not been set yet (e.g. placement created by staff without
     * financial field access, rates to be filled in later by Accounts).
     */
    public function getMonthlyMarginAttribute(): ?float
    {
        if (is_null($this->client_billing_rate) || is_null($this->worker_payout_rate)) {
            return null;
        }

        return (float) $this->client_billing_rate - (float) $this->worker_payout_rate;
    }

    public function monthlyRecords(): HasMany
{
    return $this->hasMany(PlacementMonthlyRecord::class);
}
}