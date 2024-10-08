<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Crypt;
use App\Models\SectionBean;
use App\Models\Attendance;
use App\Models\Section;

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
        'name',
        'password',
        'status'
    ];

    protected $appends = [
        'uuid'
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
