<?php

namespace App\Http\Controllers\Service;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //
    public function getAdminList(Request $request) {
        $q = $request->input('q');

        $p = $request->input('p', '1');
        $limit = $request->input('limit', "10");
        $offset = ($p - 1) * $limit;

        $sql = "SELECT * FROM `admin` WHERE operation_id = '1'";

        $total = count( DB::select($sql));

        if ($q) {
            $sql .= " AND username  like '%{$q}%'";

        }

        $sql .= " limit {$limit} offset {$offset}";

        $arr['total'] = $total;
        $arr['list'] = DB::select($sql);


        $role =$request->session()->get('role','');

        $arr['role'] = $role;

        return $this->build_return_json(1, $arr, 'success');

    }

    public function changeStatus(Request $request) {
        $id = $request->input('id');
        $type = $request->input('type');

        if (!$id || !$type) return $this->build_return_json(0,[], "缺少必要参数");

        $data = Admin::where('operation_id', 1)->where('id', $id)->first();

        if ($data->count() > 0) {
            if ($type == "true") {
                $data->status = 1;
            } else {
                $data->status = 0;
            }

            $data->update();
        }

    }

    //获取用户信息
    public function getAdminInfo(Request $request) {
        $id = $request->input('id');
        $data = Admin::where('operation_id', 1)->where('id', $id)->first();
        if (empty($data)) return $this->build_return_json(0, [], '无此用户');
        return $this->build_return_json(1, $data, 'success');

    }

    //新增&编辑
    public function addAdmin(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile   = $request->input('mobile');
        $email    = $request->input('email');
        $id       = $request->input('id');


        if(!(preg_match("/^1[34578]\d{9}$/", $mobile))) return $this->build_return_json(0, [], '手机号码格式不对');
        if(!(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $email))) return $this->build_return_json(0, [], '邮箱格式不对');

        if ($id) {
            $data = Admin::where('operation_id', 1)->where('id', $id)->first();
            if (empty($data)) return $this->build_return_json(0, [], '数据错误');
            $data->email = $email;
            $data->mobile = $mobile;
            $data->update();
        } else {
            if (mb_strlen($username) > 10 || mb_strlen($username) < 5) return $this->build_return_json(0, [], '用户名为5～10个字符');
            if (!$username || !$password || !$mobile || !$email) return $this->build_return_json(0,[], "请填写必要参数");
            $data = Admin::where('operation_id', 1)->where('username', $username)->first();
            if (empty($data)) {
                $admin = DB::insert('INSERT INTO `admin`(username, password, email, mobile) VALUES ( ?, ? , ? , ?)',[$username, $password, $email, $mobile]);
            } else{
                return $this->build_return_json(0, [], '用户名已存在');
            }
        }

        return $this->build_return_json(1, [], '操作成功');

    }

    public function delAdmin(Request $request) {
        $id = $request->input('id');
        $data = Admin::where('operation_id', 1)->where('id', $id)->first();
        if (empty($data)) return $this->build_return_json(0, [], '用户不存在');
        $data->operation_id = 0;
        $data->update();

        return $this->build_return_json(1, [], '删除成功');
    }



}
