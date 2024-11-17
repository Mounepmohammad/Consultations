<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class consultation extends Model
{
    protected $fillable = [
        'question', 'answer', 'price','user_state','expert_state'
    ];
    /**
     * Get the user that owns the consultation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function expert()
    {
        return $this->belongsTo(expert::class, 'expert_id', 'id');
    }
}
