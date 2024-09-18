<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    private $fillable = [
        'section',
        'description',
        'time_in',
        'time_out',
        'time_rendered'
    ];
}