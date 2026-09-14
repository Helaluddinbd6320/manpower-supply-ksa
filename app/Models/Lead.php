<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person',
        'phone_number',
        'email',
        'website',
        'saudi_city_id',
        'office_address',
        'business_category',
        'source',
        'status',
        'next_follow_up_date',
        'notes',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'source' => LeadSource::class,
            'status' => LeadStatus::class,
            'next_follow_up_date' => 'date',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(SaudiCity::class, 'saudi_city_id');
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class)->latest('contacted_at');
    }
}