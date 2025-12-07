<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'icon',
    ];

    // التصنيف الأب
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // التصنيفات الفرعية
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // الخدمات المرتبطة بهذا التصنيف
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
    
    public function approvedServices()
{
    return $this->hasMany(Service::class)->where('status', 'approved');
}

}
