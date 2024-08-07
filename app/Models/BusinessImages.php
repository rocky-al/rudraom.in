<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessImages extends Model
{
    use HasFactory;

    public function getUpdatedAtAttribute($value)
        {
            return date('j F Y', strtotime($value));
        }

    protected $table = 'business_images';

    protected $fillable = [
        'name',
    ];

}
