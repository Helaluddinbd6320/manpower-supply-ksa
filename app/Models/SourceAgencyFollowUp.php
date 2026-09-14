<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourceAgencyFollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_agency_id',
        'contacted_by',
        'contacted_at',
        'note',
        'next_follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
            'next_follow_up_date' => 'date',
        ];
    }

    public function sourceAgency(): BelongsTo
    {
        return $this->belongsTo(SourceAgency::class);
    }

    public function contactedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }
}