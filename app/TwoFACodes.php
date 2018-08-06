<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwoFACodes extends Model
{
    //
    protected $fillable = ['user_id', 'code'];
}
