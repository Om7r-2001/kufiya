<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'country',
        'city',
        'avatar',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
     // === العلاقات ===

    // الخدمات التي يقدمها المستخدم كبائع
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'user_id');
    }

    // الطلبات التي اشتراها (كمشتري)
    public function ordersAsBuyer(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // الطلبات التي وصلته كبائع
    public function ordersAsSeller(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    // الرسائل المرسلة
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // الرسائل المستلمة
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // المفضلة
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    // التقييمات التي كتبها (كمشتري)
    public function writtenReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'buyer_id');
    }

    // التقييمات التي استلمها (كبائع)
    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'seller_id');
    }
    //الاشعارات 
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)
                    ->where('is_read', false)
                    ->latest();
    }

}