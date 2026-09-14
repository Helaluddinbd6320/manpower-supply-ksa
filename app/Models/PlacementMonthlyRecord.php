<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementMonthlyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'placement_id',
        'month',
        'year',
        'duty_days',
        'client_billing_rate_snapshot',
        'worker_payout_rate_snapshot',
        'client_amount',
        'worker_amount',
        'client_payment_status',
        'client_paid_at',
        'worker_payment_status',
        'worker_paid_at',
        'notes',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'duty_days' => 'integer',
            'client_billing_rate_snapshot' => 'decimal:2',
            'worker_payout_rate_snapshot' => 'decimal:2',
            'client_amount' => 'decimal:2',
            'worker_amount' => 'decimal:2',
            'client_payment_status' => PaymentStatus::class,
            'client_paid_at' => 'datetime',
            'worker_payment_status' => PaymentStatus::class,
            'worker_paid_at' => 'datetime',
        ];
    }

    /**
     * Auto-calculate prorated amounts (and snapshot the rates used)
     * whenever duty_days is set/changed, based on the parent placement's
     * current rates at save time.
     */
    protected static function booted(): void
    {
        static::saving(function (PlacementMonthlyRecord $record) {
            $placement = $record->placement ?? Placement::find($record->placement_id);

            if (! $placement) {
                return;
            }

            $daysInMonth = Carbon::createFromDate($record->year, $record->month, 1)->daysInMonth;

            if (! is_null($placement->client_billing_rate)) {
                $record->client_billing_rate_snapshot = $placement->client_billing_rate;
                $record->client_amount = round(
                    ($record->duty_days / $daysInMonth) * (float) $placement->client_billing_rate,
                    2
                );
            }

            if (! is_null($placement->worker_payout_rate)) {
                $record->worker_payout_rate_snapshot = $placement->worker_payout_rate;
                $record->worker_amount = round(
                    ($record->duty_days / $daysInMonth) * (float) $placement->worker_payout_rate,
                    2
                );
            }
        });
    }

    public function placement(): BelongsTo
    {
        return $this->belongsTo(Placement::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * This month's margin (client amount − worker amount), computed
     * from the snapshotted amounts.
     */
    public function getMonthlyMarginAttribute(): ?float
    {
        if (is_null($this->client_amount) || is_null($this->worker_amount)) {
            return null;
        }

        return (float) $this->client_amount - (float) $this->worker_amount;
    }

    /**
     * Number of days in this record's month (for display/reference).
     */
    public function getDaysInMonthAttribute(): int
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;
    }
}