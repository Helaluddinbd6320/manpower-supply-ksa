<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\LanguageProficiency;
use App\Enums\LocationType;
use App\Enums\WorkerStatus;
use App\Enums\Nationality;
use App\Models\JobOrderWorker;
use App\Enums\WorkerOrderStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Support\Str;

class Worker extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     *
     * NOTE: worker_id is intentionally NOT fillable — it is auto-generated
     * in the booted() method below (format: CHI-YYYY-XXXX).
     */
    protected $fillable = [
        // ---- Basic Info (Step 11) ----
        'name', // English only — no separate Bangla name field
        'passport_number',
        'passport_issue_date',
        'passport_expiry_date',
        'date_of_birth',
        'gender',
        'religion',
        'nationality',
        'mobile_number',
        'emergency_contact_number',
        'district',
        'upazila',
        'marital_status',

        // ---- Professional Info (Step 11) ----
        'experience_years',
        'experience_description',
        'arabic_proficiency',
        'english_proficiency',
        'education_qualification',
        'trade_test_certificate',
        'driving_license_type',

        // ---- Location Status (Step 14) ----
        'location_type',
        'iqama_number',
        'iqama_photo_path',
        'iqama_expiry_date',
        'iqama_status',
        'border_number',
        'iqama_occupation',
        'current_city',
        'kafala_transfer_interested',

        // ---- Employment Status (Step 15) ----
        'employment_status',
        'contract_end_date',
        'change_reason',
        'notice_period',
        'available_from_date',
        'expected_salary',
        'status',

        // ---- System Info ----
        'entered_by', // staff user id who entered this worker (audit trail)
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            // Basic Info
            'passport_issue_date' => 'date',
            'passport_expiry_date' => 'date',
            'date_of_birth' => 'date',

            // Professional Info
            'experience_years' => 'integer',
            'arabic_proficiency' => LanguageProficiency::class,
            'english_proficiency' => LanguageProficiency::class,

            // Location Status
            'location_type' => LocationType::class,
            'iqama_expiry_date' => 'date',
            'iqama_status' => \App\Enums\IqamaStatus::class,
            'kafala_transfer_interested' => 'boolean',

            // Employment Status
            'contract_end_date' => 'date',
            'available_from_date' => 'date',
            'expected_salary' => 'decimal:2',
            'status' => WorkerStatus::class,

            'nationality' => Nationality::class,
        ];
    }

    /**
     * Auto-generate the unique Worker ID (CHI-YYYY-XXXX) on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Worker $worker) {
            if (empty($worker->worker_id)) {
                $worker->worker_id = static::generateWorkerId();
            }
        });
    }

    /**
     * Generate a unique worker_id in the format CHI-YYYY-XXXX.
     * XXXX is a zero-padded sequential number, reset conceptually per year
     * (based on count of workers already created in the current year).
     */
    protected static function generateWorkerId(): string
    {
        $year = now()->year;
        $prefix = "CHI-{$year}-";

        $lastWorker = static::where('worker_id', 'like', "{$prefix}%")
            ->orderByDesc('worker_id')
            ->first();

        $nextNumber = 1;

        if ($lastWorker) {
            $lastNumber = (int) Str::afterLast($lastWorker->worker_id, '-');
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships (Step 12 / Step 13)
    |--------------------------------------------------------------------------
    */

    /**
     * A worker can be interested in multiple job categories (Step 13 pivot).
     */
    public function jobCategories(): BelongsToMany
    {
        return $this->belongsToMany(JobCategory::class, 'worker_job_categories');
    }

    /**
     * Staff member who entered this worker's record (audit trail).
     */
    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships (Step 20 — Worker Documents)
    |--------------------------------------------------------------------------
    */

    /**
     * All documents belonging to this worker (all types, full history).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(WorkerDocument::class);
    }

    /**
     * All certificate documents for this worker (multi-file type).
     */
    public function certificates(): HasMany
    {
        return $this->documents()->where('document_type', DocumentType::Certificate);
    }

    /**
     * Most recently uploaded CV.
     *
     * NOTE: uses ofMany() with the constraint INSIDE the closure rather than
     * latestOfMany() with a chained ->where() before it. Chaining ->where()
     * before latestOfMany() does not reliably fold into the aggregate
     * subquery Eloquent builds for "of many" relationships, which can cause
     * the relation to silently resolve to null even when a matching row
     * exists. Passing the constraint into ofMany()'s closure applies it
     * correctly to both the outer query and the grouped subquery.
     */
    public function cv(): HasOne
    {
        return $this->hasOne(WorkerDocument::class)
            ->ofMany(['created_at' => 'max'], function ($query) {
                $query->where('document_type', DocumentType::Cv);
            });
    }

    /**
     * Most recently uploaded photo.
     */
    public function photo(): HasOne
    {
        return $this->hasOne(WorkerDocument::class)
            ->ofMany(['created_at' => 'max'], function ($query) {
                $query->where('document_type', DocumentType::Photo);
            });
    }

    /**
     * Most recently uploaded passport copy.
     */
    public function passport(): HasOne
    {
        return $this->hasOne(WorkerDocument::class)
            ->ofMany(['created_at' => 'max'], function ($query) {
                $query->where('document_type', DocumentType::Passport);
            });
    }

    /**
     * Most recently uploaded iqama copy.
     */
    public function iqama(): HasOne
    {
        return $this->hasOne(WorkerDocument::class)
            ->ofMany(['created_at' => 'max'], function ($query) {
                $query->where('document_type', DocumentType::Iqama);
            });
    }

    public function jobOrders(): BelongsToMany
    {
        return $this->belongsToMany(JobOrder::class, 'job_order_worker')
            ->using(JobOrderWorker::class)
            ->withPivot(['status', 'contacted_at', 'contacted_by', 'notes'])
            ->withTimestamps();
    }


    /**
     * Workers who are free/available for placement.
     */
    public function scopeAvailable(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('employment_status', 'free_available');
    }

    /**
     * The job order this worker is currently Confirmed on, if any.
     * (Assumes at most one active confirmation is the common case,
     * but does not enforce it at the DB level — see Step 30 notes.)
     */
    public function confirmedJobOrder(): ?JobOrder
    {
        return $this->jobOrders()
            ->wherePivot('status', WorkerOrderStatus::Confirmed->value)
            ->first();
    }

    /**
     * Whether this worker is Confirmed on any job order other than
     * the given one. Used to show a warning flag on other orders
     * where this worker is still Shortlisted/Contacted/Interested.
     */
    public function isConfirmedOnOtherOrder(int $excludingJobOrderId): bool
    {
        return $this->jobOrders()
            ->wherePivot('status', WorkerOrderStatus::Confirmed->value)
            ->where('job_orders.id', '!=', $excludingJobOrderId)
            ->exists();
    }


    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        $photo = $this->documents()->where('document_type', 'photo')->first();

        if (! $photo) {
            return null;
        }

        return Cache::remember(
            "worker-photo-url-{$this->id}",
            now()->addHours(6),
            fn() => Storage::disk('r2')->temporaryUrl($photo->file_path, now()->addHours(6))
        );
    }

    public function scopeValidIqamaOnly(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('iqama_status', \App\Enums\IqamaStatus::Valid->value);
    }

    public function sourcingAgent(): BelongsTo
{
    return $this->belongsTo(Agent::class, 'sourcing_agent_id');
}
}