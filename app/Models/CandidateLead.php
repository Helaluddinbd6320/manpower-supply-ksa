<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CandidateLeadFollowUp;

class CandidateLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'destination_country_id',
        'job_category_id',
        'age',
        'area',
        'source',
        'status',
        'photo_path',
        'passport_copy_path',
        'nationality_country_id',
        'next_follow_up_date',
        'notes',
        'entered_by',
    ];

    protected $casts = [
        'next_follow_up_date' => 'date',
    ];

    public function destinationCountry()
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function nationalityCountry()
    {
        return $this->belongsTo(Country::class, 'nationality_country_id');
    }

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function followUps()
    {
        return $this->hasMany(CandidateLeadFollowUp::class)->orderByDesc('contacted_at');
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}
