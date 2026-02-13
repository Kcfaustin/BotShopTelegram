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
        'file_path',
        'file_disk',
        'is_active',
        'extras',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extras' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPriceLabelAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ').' '.$this->currency;
    }
}
