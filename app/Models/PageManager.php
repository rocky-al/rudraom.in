<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageManager extends Model
{
    use HasFactory;

    public function getUpdatedAtAttribute($value)
        {
            return date('j F Y', strtotime($value));
        }

    protected $table = 'master_page_managers';

    protected $fillable = [
        'page_slug',
        'page_name',
        'title',
        'description',
        'meta_keyword',
        'meta_description',
    ];

    protected $hidden = [
        'remember_token',
    ];
}
