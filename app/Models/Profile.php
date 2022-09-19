<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $appends = ['fullname'];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        return $this->name ." ".$this->last_name;
    }



}
