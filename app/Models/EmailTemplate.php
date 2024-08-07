<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    public function getUpdatedAtAttribute($value)
    {
        return date('j F Y', strtotime($value));
    }

}
