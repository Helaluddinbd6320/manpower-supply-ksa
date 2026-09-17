<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'group',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (JobCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name_en ?? $category->name);
            }
        });
    }

    /**
     * এই ক্যাটাগরিতে আগ্রহী worker-রা (Many-to-Many)
     * Step 13-এ worker_job_categories pivot তৈরি হলে এটি কাজ করবে
     */
    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'worker_job_categories')
            ->withTimestamps();
    }

    /**
     * এই ক্যাটাগরির জব অর্ডারসমূহ
     * Step 24-এ job_orders টেবিল তৈরি হলে এটি কাজ করবে
     */
    public function jobOrders(): HasMany
    {
        return $this->hasMany(JobOrder::class);
    }

    public function sourceAgencies(): BelongsToMany
    {
        return $this->belongsToMany(SourceAgency::class, 'source_agency_job_categories');
    }

    /**
     * এই ক্যাটাগরিতে থাকা Quotation Item-সমূহ
     * quotation_items টেবিলে job_category_id কলাম অ্যাড হলে এটি কাজ করবে
     */
    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }
}