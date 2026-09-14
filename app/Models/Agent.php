<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile_number',
        'avatar_path',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'next_follow_up_date' => 'date',

    ];

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class, 'sourcing_agent_id');
    }

    /**
     * Cached signed R2 URL for the agent's avatar. Follows the same
     * 6-hour Cache::remember() pattern used for worker photo/document
     * URLs to avoid re-signing on every page load.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        $cacheKey = 'r2-temp-url:' . md5($this->avatar_path);

        return Cache::remember($cacheKey, now()->addHours(6), function () {
            return Storage::disk('r2')->temporaryUrl(
                $this->avatar_path,
                now()->addHours(6)->addMinutes(5)
            );
        });
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(AgentFollowUp::class)->latest('contacted_at');
    }
}
