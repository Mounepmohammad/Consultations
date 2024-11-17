<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class expert extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'name', 'email', 'password','image','specialization','consult_price','account'
    ];
/**
 * Get all of the consult for the expert
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
public function consult()
{
    return $this->hasMany(consultation::class, 'expert_id', 'id');
}
public function meet()
{
    return $this->hasMany(meeting::class, 'expert_id', 'id');
}

public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
