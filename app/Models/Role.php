<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    protected $table = 'role';
    protected $primaryKey = 'id';

    public function getRoleList() {
        $list = Role::where('operation_id', 1)->get();

        return $list;
    }
}
