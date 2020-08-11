<?php

namespace App\Http\Controllers\Service;

use App\Models\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;

class PowerController extends Controller
{

     public function getRoleList() {
         $roles = Role::where('operation_id', 1)->get();

         $list = [];
//
//         foreach ($roles as $index => $role) {
//
//             $list[$index] = $role;
//             $list[$index]['children'] = [];
//
//
//             $auth_ids = $role->auth_ids;
//             $auth_ids = explode(",", $auth_ids);
//
//             $auths = Auth::where('operation_id', 1)->whereIn('id', $auth_ids)->get()->toArray();
//
//             if (count($auths) > 0) {
//                 foreach ($auths as $auth) {
//                     $tmp = $auth;
//                     $tmp['list'] = Auth::where('operation_id', 1)->where('pid', $auth['id'])->get()->toArray();
//                     $list[$index]['list'][] = $tmp;
//                 }
//             }
//
//             var_dump($list);die;
//
//         }
         return $this->build_return_json(1, $roles, 'success');
     }

     public function getRoleList1() {

     }


}
