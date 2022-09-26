<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeRequest extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function requests(){
        return $this->hasMany(Request::class);
    }

    public function users(){
        return $this->belongsToMany(User::class,"type_user");
    }
}
