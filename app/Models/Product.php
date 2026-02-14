<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'category_id',
        'file_path',
        'telegram_file_id',
        'file_disk',
        'is_active',
        'extras',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extras' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPriceLabelAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ').' '.$this->currency;
    }
}
