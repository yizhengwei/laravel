<?php

namespace App\Http\Controllers\Service;

use App\Models\Admin;
use App\Models\Role;
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

        $sql = "SELECT * FROM `admin` a WHERE operation_id = '1'";


        if ($q) {
            $sql .= " AND username  like '%{$q}%'";

        }

        $sql_count = "SELECT count(*) as num FROM ({$sql}) a ";

        $sql = "SELECT a.*, b.name FROM ({$sql}) a LEFT JOIN `role` b ON a.role_id = b.id  ORDER BY a.id limit {$limit} offset {$offset}";

        $a = DB::select($sql_count);
        $a = json_encode($a, true);
        $a = json_decode($a,true);


        $arr['total'] = $a[0]['num'];
        $arr['list'] = DB::select($sql);

        $a = new Role();
        $arr['role'] = $a->getRoleList();

        return $this->build_return_json(1, $arr, 'success');

    }

    public function changeStatus(Request $request) {
        $id = $request->input('id');
        $type = $request->input('type');

        if (!$id || !in_array($type,[0,1])) return $this->build_return_json(0,[], "缺少必要参数");

        $data = Admin::where('operation_id', 1)->where('id', $id)->first();

        if ($data->count() > 0) {
            if ($type == 1) {
                $data->status = 1;
            } else {
                $data->status = 0;
            }
            $data->update();
        }

        return $this->build_return_json(1,[], 'success');

    }

    //新增&编辑
    public function addAdmin(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile   = $request->input('mobile');
        $email    = $request->input('email');
        $role_id  = $request->input('role_id');
        $id       = $request->input('id');


        if(!(preg_match("/^1[34578]\d{9}$/", $mobile))) return $this->build_return_json(0, [], '手机号码格式不对');
        if(!(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $email))) return $this->build_return_json(0, [], '邮箱格式不对');

        if ($id) {
            $data = Admin::where('operation_id', 1)->where('id', $id)->first();
            if (empty($data)) return $this->build_return_json(0, [], '数据错误');
            $data->email = $email;
            $data->mobile = $mobile;
            $data->role_id = $role_id;
            $data->update();
        } else {
            if (mb_strlen($username) > 10 || mb_strlen($username) < 5) return $this->build_return_json(0, [], '用户名为5～10个字符');
            if (!$username || !$password || !$mobile || !$email) return $this->build_return_json(0,[], "请填写必要参数");
            $data = Admin::where('operation_id', 1)->where('username', $username)->first();
            if (empty($data)) {
                $admin = DB::insert('INSERT INTO `admin`(username, password, email, mobile, role_id) VALUES ( ?, ? , ? , ?, ?)',[$username, $password, $email, $mobile, $role_id]);
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
