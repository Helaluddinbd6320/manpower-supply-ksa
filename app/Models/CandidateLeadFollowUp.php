<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateLeadFollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_lead_id',
        'contacted_by',
        'contacted_at',
        'note',
        'next_follow_up_date',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'next_follow_up_date' => 'date',
    ];

    public function candidateLead()
    {
        return $this->belongsTo(CandidateLead::class);
    }

    public function contactedBy()
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }
}