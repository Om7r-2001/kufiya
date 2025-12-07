<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBid extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'amount',
        'delivery_days',
        'message',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
