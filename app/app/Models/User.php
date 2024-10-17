<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Crypt;
use App\Models\Attendance;
use App\Models\Section;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'password',
        'status'
    ];

    protected $appends = [
        'uuid',
        'name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sections() {
        return $this->belongsToMany(Section::class, 'section_beans', 'user', 'section');
    }


    public function attendances() {
        return $this->hasMany(Attendance::class, 'user', 'id');
    }

    public function getUuidAttribute() {
        return base64_encode($this->id);
    }

    public function getNameAttribute() {
        return $this->first_name." ".$this->last_name;
    }

    private function disableDate($currentDate)
    {

        $currentWeek = $currentDate->copy()->format('l');

        $disableWeek = collect(request()->weekDaysToDisable)->contains(function ($i) use ($currentWeek) {
            return strtolower(Carbon::parse($i)->timezone('Asia/Manila')->format('l')) === strtolower($currentWeek);
        });

        $disableDates = collect(request()->excludeDates)->some(function ($i) use ($currentDate) {
            return Carbon::parse($currentDate)->timezone('Asia/Manila')->startOfDay()->eq(Carbon::parse($i)->timezone('Asia/Manila')->startOfDay()->subDay());
        });

        return  $disableWeek || $disableDates;
    }

    private function getRemarks($attendance) {
        // return optional($attendance)->toArray();
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
