<?php

namespace App\Http\Controllers\Service;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
    public function login(Request $request){
        $username = $request->input('username','');
        $password = $request->input('password','');


        if (empty($username) || empty($password)) return $this->build_return_json(0, [], "请填写必填项");

        $data = Admin::where('operation_id', 1)->where('status', 1)->where('username', $username)->first();
        if ($data == null) {
            return $this->build_return_json(0, [], "该用户不存在");
        } else {
            if ($password != $data->password) return $this->build_return_json(0, [],'密码错误');
        }

        $list = Admin::where('operation_id', 1)->where('username', $username)->first();
        $role = Role::where('operation_id', 1)->where('id', $list->role_id)->first()->name;

        return $this->build_return_json(1, $list, "欢迎".$role."登录成功");

    }
}
