<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'meta_title',
        'meta_description',
        'h1_heading',
        'intro_content',
        'highlights',
        'city_name',
        'job_category_name',
        'page_type',
        'is_published',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'highlights' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}