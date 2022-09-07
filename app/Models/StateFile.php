<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateFile extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function files(){
        return $this->hasMany(File::class);
    }
}
