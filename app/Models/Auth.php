<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    //
    protected $table = 'auth';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
