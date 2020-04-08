<?php

namespace App\Http\Controllers\V1;

use App\Models\Auth;
use App\Models\Member;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class wxLoginController extends Controller
{
    //
    public function login(Request $request) {

        $code = $request->input('code');

        $param=array();
        $param[]='appid='.env('APPID');
        $param[]='secret='.env('SECRET');
        $param[]='js_code='.$request->code;
        $param[]='grant_type=authorization_code';
        $params=implode('&',$param);    //用&符号连起来
        $url = env('WECHAT_GET_OPEN_ID').'?'.$params;


        $client = new \GuzzleHttp\Client([
            'timeout' => 60
        ]);

        $res = $client->request('POST',$url);
        $data = json_decode($res->getBody()->getContents(),true);

        $result = Member::where('openid', $data['openid'])->first();
        $result = json_decode(json_encode($result), true);

        if ($result == null) {
            DB::beginTransaction();
            try {
                DB::table('member')->insert(['openid' => $data['openid']]);
                DB::commit();
                return $this->build_return_json(1, '', 'success');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->build_return_json(0, [], $e->getMessage());
            }
        }else if($result['nickname']!=NULL){ //非首次登录，且已授权，存用户信息
            $member = array();
            $member['nickname'] = $result['nickname'];
            $member['avatarUrl'] = $result['avatarUrl'];
            return $member;
        }

        return $this->build_return_json(1, $data, 'success');

    }

    public function save(Request $request) {
        DB::beginTransaction();
        try{
            DB::table('member')->where('openid',$request->openid)->update([
                'avatarUrl'=>$request->avatarUrl,
                'nickname'=>$request->nickName
            ]);
            DB::commit();
        }catch(\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return $this->build_return_json(1, [], '授权成功');
    }

    public function userLogin(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');
        if (!$username || !$password) return $this->build_return_json(0, [], '请输入用户名或密码');

        $data = Member::where('operation_id', 1)->where('username', $username)->first();

        if ($data == null) {
            return $this->build_return_json(0, [], "该用户不存在");
        } else {
            if ($password != $data->password) return $this->build_return_json(0, [],'密码错误');
        }

        return $this->build_return_json(1, [], "欢迎登录");
    }
}
