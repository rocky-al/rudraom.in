<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faqs extends Model
{
    use HasFactory;

    public function getUpdatedAtAttribute($value)
        {
            return date('j F Y', strtotime($value));
        }

    protected $table = 'master_faqs';

    protected $fillable = [
        'faq',
        'description',
        'type',
    ];

    protected $hidden = [
        'remember_token',
    ];
}
