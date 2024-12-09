<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'price',
        'unit',
        'short_description',
        'full_description',
        'image_preview',
        'images',
        'is_published',
        'seller_id',
    ];

    protected $casts = [
        'images' => 'array',
        'is_published' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }


    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('short_description', 'like', "%{$term}%");
    }
}
