<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\DaysPresent;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'description',
        'time_in',
        'time_out',
    ];


    public function users() {
        return $this->belongsToMany(User::class, 'section_beans', 'section', 'user');
    }

    public function daysPresents() {
        return $this->belongsTo(DaysPresent::class, 'section', 'id');
    }
}
