<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessList extends Model
{
    use HasFactory;

    public function getUpdatedAtAttribute($value)
        {
            return date('j F Y', strtotime($value));
        }

    protected $table = 'businesses';

    protected $fillable = [
        'faq',
        'description',
        'type',
    ];

    
}
