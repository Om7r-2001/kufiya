<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'service_id',
        'project_id', 
        'buyer_id',
        'seller_id',
        'price',
        'status',
        'notes',
        'delivery_date',
        'completed_at',
        'total_price',
        'payment_status',
        'payment_method',
        'platform_fee',
        'seller_earnings',
        'paid_at',
        'released_at',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function review(): HasOne
    {
        // إن كان لكل طلب تقييم واحد
        return $this->hasOne(Review::class);
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    protected $casts = [
        'paid_at'     => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at'  => 'datetime',
    ];
}
