<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'lecturer_id',
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id')
            ->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(\App\Models\Material::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function discussions()
    {
        return $this->hasMany(\App\Models\Discussion::class);
    }
}
