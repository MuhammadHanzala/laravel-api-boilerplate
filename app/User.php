<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasMediaTrait, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'imageUrl', 'isVerified', '2fa'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    public function verifyUser()
    {
        return $this->hasOne('App\VerifyUser');
    }

    public function categories(){
        return $this->hasMany('App\Category');
    }

    public function subCategories(){
        return $this->hasMany('App\SubCategory');
    }

    public function todos(){
        return $this->hasMany('App\TodoItem');
    }

    public function groups(){
        return $this->hasMany('App\Group','created_by');
    }

    public function friends(){
        return $this->hasMany('App\Contact');
    }
    public function iAmFriend(){
        return $this->hasMany('App\Contact','friend_id');
    }
    public function pendingRequests(){
        return $this->hasMany('App\Contact');
    }

}
