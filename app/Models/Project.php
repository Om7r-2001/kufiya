<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'image_path',
        'budget_min',
        'budget_max',
        'delivery_days',
        'description',
        'status',
        'views_count',
        'bids_count',
        'selected_bid_id',
    ];

    // صاحب المشروع (المشتري)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // العروض
    public function bids()
    {
        return $this->hasMany(ProjectBid::class);
    }

    // العرض المقبول (إن وجد)
    public function selectedBid()
    {
        return $this->belongsTo(ProjectBid::class, 'selected_bid_id');
    }

    // استخدام slug في الروت
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    // إنشاء slug تلقائياً عند الإنشاء
    protected static function booted()
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title) . '-' . uniqid();
            }
        });
    }
}

