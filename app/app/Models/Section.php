<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Carbon;
use DateTime;

class Section extends Model
{
    use HasFactory;

    protected $appends = ['mid_point'];
    protected $fillable = [
        'section',
        'description',
        'time_in',
        'time_out',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'section_beans', 'section', 'user');
    }

    public function getMidPointAttribute()
    {
        $startTime = new DateTime('2024-10-08 23:00:00');
        $endTime = new DateTime('2024-10-08 23:50:00');

        // Calculate the difference in seconds
        $interval = $endTime->getTimestamp() - $startTime->getTimestamp();

        // Find the midpoint in seconds
        $midpointTimestamp = $startTime->getTimestamp() + ($interval / 2);

        // Create a DateTime object for the midpoint
        $midpointTime = (new DateTime())->setTimestamp($midpointTimestamp);

        return $midpointTime->format('H:i:s');
    }
}
