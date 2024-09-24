<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Section;

class SectionBean extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'section'
    ];

    public function userDetails() {
        return $this->belongsTo(User::class, 'user', 'id');
    }

    public function sectionDetails() {
        return $this->belongsTo(Section::class, 'section', 'id');
    }
}
