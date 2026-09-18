<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_number',
        'lead_id',
        'client_company_name',
        'quotation_date',
        'header_image_path',
        'client_info_content',
        'terms_content',
        'signature_content',
        'seal_image_path',
        'footer_content',
        'status',
        'created_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Quotation $quotation) {
            if (blank($quotation->quotation_number)) {
                $year = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $quotation->quotation_number = sprintf('QT-%d-%04d', $year, $count);
            }
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getGrandTotalAttribute(): float
    {
        return $this->items->sum(fn ($item) => $item->qty * $item->monthly_rate_per_worker);
    }
}