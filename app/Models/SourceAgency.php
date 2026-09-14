<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\SourceAgencyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceAgency extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_name',
        'country_id',
        'contact_person',
        'phone_number',
        'email',
        'website',
        'office_address',
        'license_number',
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
            'status' => SourceAgencyStatus::class,
            'next_follow_up_date' => 'date',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function jobCategories(): BelongsToMany
    {
        return $this->belongsToMany(JobCategory::class, 'source_agency_job_categories');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(SourceAgencyFollowUp::class)->latest('contacted_at');
    }
}