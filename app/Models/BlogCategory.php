<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class BlogCategory extends Model
{
    use HasFactory;
    use Sluggable;

    public function getUpdatedAtAttribute($value)
        {
            return date('j F Y', strtotime($value));
        }

    protected $table = 'master_blog_category';

    protected $fillable = [
        'name',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

}
