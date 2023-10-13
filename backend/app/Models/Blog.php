<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'blog_title_slug',
        'short_description',
        'long_description',
        'blog_image',
        'blog_video_link',
        'category_id',
        'is_active',
        'user_id'
    ];

    public function blogCategory()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
