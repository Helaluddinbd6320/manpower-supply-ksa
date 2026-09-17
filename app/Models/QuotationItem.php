<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'job_category_id',
        'nationality',
        'gender',
        'pricing_type',
        'hours_per_day',
        'rate_per_hour',
        'qty',
        'monthly_rate_per_worker',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'hours_per_day' => 'decimal:2',
        'rate_per_hour' => 'decimal:2',
        'monthly_rate_per_worker' => 'decimal:2',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function getRatePerDayAttribute(): ?float
    {
        if ($this->pricing_type !== 'hourly' || blank($this->rate_per_hour) || blank($this->hours_per_day)) {
            return null;
        }

        return round($this->rate_per_hour * $this->hours_per_day, 2);
    }

    public function getGrandTotalAttribute(): float
    {
        return $this->qty * $this->monthly_rate_per_worker;
    }
}