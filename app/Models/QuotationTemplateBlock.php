<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationTemplateBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'content',
        'image_path',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}