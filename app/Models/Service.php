<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'price',
        'delivery_time',
        'level',
        'status',
        'allow_messages_before_order',
        'rating_avg',
        'rating_count',
    ];

    // البائع صاحب الخدمة
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // التصنيف
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // الصور
    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class);
    }

    // الطلبات على هذه الخدمة
    public function orders(): HasMany
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    // التقييمات
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // من أضافها للمفضلة
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeApproved($query)
{
    return $query->where('status', 'approved');
}

}
