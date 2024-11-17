<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class meeting extends Model
{
    protected $fillable = [
        'date','user_state','expert_state','state'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function expert()
    {
        return $this->belongsTo(expert::class, 'expert_id', 'id');
    }
}
