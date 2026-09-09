<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'capacity',
        'is_active',
    ];
}
