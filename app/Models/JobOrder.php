<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\WorkerOrderStatus;
use App\Models\JobOrderWorker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class JobOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_category_id',
        'company_name',
        'quantity_needed',
        'salary_min',
        'salary_max',
        'contract_duration_months',
        'requirements',
        'order_date',
        'deadline',
        'status',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_needed' => 'integer',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'contract_duration_months' => 'integer',
            'order_date' => 'date',
            'deadline' => 'date',
            'status' => OrderStatus::class,
        ];
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

public function workers(): BelongsToMany
{
    return $this->belongsToMany(Worker::class, 'job_order_worker')
        ->using(JobOrderWorker::class)
        ->withPivot(['status', 'contacted_at', 'contacted_by', 'notes'])
        ->withTimestamps();
}


/*
|--------------------------------------------------------------------------
| Matching Logic (Step 27)
|--------------------------------------------------------------------------
*/

/**
 * Workers who match this job order's category and are currently
 * free/available, excluding workers already shortlisted/attached
 * to this specific job order.
 */
public function matchingWorkers(): Builder
{
    $alreadyAttachedIds = $this->workers()->pluck('workers.id');

    return Worker::query()
        ->available()
        ->whereHas('jobCategories', function (Builder $query) {
            $query->where('job_categories.id', $this->job_category_id);
        })
        ->whereNotIn('id', $alreadyAttachedIds);
}

/**
 * Count of matching (not-yet-shortlisted) available workers.
 */
public function matchingWorkersCount(): int
{
    return $this->matchingWorkers()->count();
}


public function placements(): HasMany
{
    return $this->hasMany(Placement::class);
}

}