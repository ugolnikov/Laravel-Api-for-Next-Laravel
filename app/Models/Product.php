<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Название таблицы в базе данных (если отличается от имени модели)
     */
    protected $table = 'products';

    /**
     * Массово заполняемые поля
     */
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

    /**
     * Типы полей для кастинга
     */
    protected $casts = [
        'images' => 'array', // Автоматическое преобразование JSON в массив
        'is_published' => 'boolean',
    ];

    /**
     * Связь: Товар принадлежит продавцу
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Скопы для упрощения запросов
     */

    // Только опубликованные товары
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Поиск по имени или описанию
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('short_description', 'like', "%{$term}%");
    }
}
