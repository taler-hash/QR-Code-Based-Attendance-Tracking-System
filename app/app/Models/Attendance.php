<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TimeLog;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'date',
        'reason'
    ];

    public function user() {
        return $this->hasOne(User::class, 'id', 'user');
    }

    public function timeLogs() {
        return $this->hasMany(TimeLog::class, 'attendance');
    }
}
