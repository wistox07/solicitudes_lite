<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'register_date' => 'datetime:d/m/Y',
        'start_date' => 'datetime:d/m/Y'

    ];
    



    public function type(){
        return $this->belongsTo(TypeRequest::class,"type_request_id");
    }

    public function state(){
        return $this->belongsTo(StateRequest::class);
    }

    public function files(){
        return $this->hasMany(File::class);
    }
    public function priority(){
        return $this->belongsTo(PriorityRequest::class);
    }
    public function satisfaction(){
        return $this->belongsTo(SatisfactionRequest::class);
    }
    public function comments(){
        return $this->hasMany(Comment::class);
    }
    public function register(){
        return $this->belongsTo(User::class,"register_id");
    }
    public function petitioner(){
        return $this->belongsTo(User::class,"petitioner_id");
    }
    public function agent(){
        return $this->belongsTo(User::class,"agent_id");
    }
}
