<?php

namespace App\Http\Controllers\Service;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
    public function login(Request $request){
        $username = $request->input('username','');
        $password = $request->input('password','');


        if (empty($username) || empty($password)) return $this->build_return_json(0, [], "请填写必填项");

        $data = Admin::where('username', $username)->first();
        if ($data == null) {
            return $this->build_return_json(0, [], "该用户不存在");
        } else {
            if ($password != $data->password) return $this->build_return_json(0, [],'密码错误');
        }
        $request->session()->put('role', $data->role);

        return $this->build_return_json(1, [], "登录成功");

    }
}
