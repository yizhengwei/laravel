<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id';
    public $timestamps = false;

//    public function generateToken()
//    {
//        $this->api_token = str_random(128);
//        $this->save();
//
//        return $this->api_token;
//
//    }


}
