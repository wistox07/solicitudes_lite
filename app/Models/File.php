<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    public function request(){
        return $this->belongsTo(Request::class);
    }

    public function state(){
        return $this->belongsTo(StateFile::class);
    }
}
