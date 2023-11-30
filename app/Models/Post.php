<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'slug',
        'color',
        'category_id',
        'content',
        'thumbnail',
        'tags',
        'published'
    ];


    protected $casts = [
        'tags' => 'array',
    ];


    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function authors(){
        return $this->BelongsToMany(User::class, 'post_user')->withPivot(['order'])->withTimestamps();
    }


}

