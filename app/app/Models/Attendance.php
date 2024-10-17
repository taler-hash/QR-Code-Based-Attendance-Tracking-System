<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TimeLog;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Attendance extends Model
{
    use HasFactory;

    protected $appends = [
        'rawDate',
        'time_in',
        'time_out'
    ];

    protected $fillable = [
        'user',
        'date',
        'reason'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'attendance');
    }

    public function getDateAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->attributes['date'])->format('l F j, Y');
    }

    public function getRawDateAttribute()
    {
        return $this->attributes['date'];
    }

    public function getTimeInAttribute()
    {
        $status = null;
        $timeLogs = $this->timeLogs->toArray();
        $section = $this->users->sections->first()->toArray();
        $sectionTimeIn = $section['time_in'];
        $midPoint = $section['mid_point'];

        $timeIns = collect($timeLogs)->filter(function ($i) use ($midPoint) {
            return Carbon::createFromFormat('H:i:s', $i['time'])->lt($midPoint);
        })->toArray();

        $timeIn = $timeIns ? Carbon::createFromFormat('H:i:s', $timeIns[0]['time']) : null;

        if ($timeIn) {

            $isLate = $timeIn->lessThan($sectionTimeIn);

            $status = $isLate ? 'late' : 'ontime';
        }

        return [
            'status' =>  $status,
            'time' => $timeIn?->format('g:i A') ?? null
        ];
    }

    public function getTimeOutAttribute()
    {
        $status = null;
        $timeLogs = $this->timeLogs->toArray();
        $section = $this->users->sections->first()->toArray();
        $midPoint = $section['mid_point'];
        $sectionTimeOut = $section['time_out'];

        $timeOuts = collect($timeLogs)->filter(function ($i) use ($midPoint) {
            return Carbon::createFromFormat('H:i:s', $i['time'])->greaterThan($midPoint);
        })->toArray();

        $timeOut = $timeOuts ? Carbon::createFromFormat('H:i:s', $timeOuts[count($timeOuts)]['time']) : null;

        if ($timeOut) {
            $isEarly = $timeOut->greaterThan($sectionTimeOut);

            $status = $isEarly ? 'early' : 'ontime';
        }

        return [
            'status' =>  $status,
            'time' => $timeOut?->format('g:i A') ?? null
        ];
    }




}
