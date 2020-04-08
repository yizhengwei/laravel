<?php

namespace App\Http\Controllers\V1;

use App\Models\Member;
use App\Models\Validate;
use App\Tool\SMS\SendTemplateSMS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ValidateController extends Controller
{
    public function sendSMS(Request $request)
    {
        $phone = $request->input('phone');

        $sendTemplateSMS = new SendTemplateSMS;
        $code="";
        $charset="1234567890";
        $_len = strlen($charset) - 1;
        for ($i = 0;$i < 6;++$i) {
            $code .= $charset[mt_rand(0, $_len)];
        }


        $result = $sendTemplateSMS->sendTemplateSMS($phone, array($code, 60), 1);

        if ($result->status == 1) {
            $tempPhone = Validate::where('phone', $phone)->first();
            if ($tempPhone == null) {
                $tempPhone = new Validate();
            }

            $tempPhone->phone = $phone;
            $tempPhone->code = $code;
            $tempPhone->deadline = date('Y-m-d H-i-s', time() + 60*60);
            $tempPhone->save();

            return $this->build_return_json(1, [], '发送成功');
        }
        return $this->build_return_json(0, [], $result->message);
    }

    public function register(Request $request) {
        $phone = $request->input('phone');
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');
        $code = $request->input('code');

        if (!$phone || !$password || !$confirm_password || !$code) return $this->build_return_json(0, [], '请填写必填项');
        if (!(preg_match("/^1[34578]\d{9}$/",  $phone))) return $this->build_return_json(0, [], "手机号码格式错误");
        if (mb_strlen($password) < 6 || mb_strlen($confirm_password) < 6) return $this->build_return_json(0, [], '密码不能为空或小于6位');
        if ($password != $confirm_password) return $this->build_return_json(0, [], '两次密码不相同');
        if (mb_strlen($code) != 6) return $this->build_return_json(0, [], '验证码为6位');

        $validate = Validate::where('phone', $phone)->first();

        $member = Member::where('phone', $phone)->first();

        if ($member == null) {
            if ($validate->code == $code) {

                if ( (date('Y-m-d H:i:s', time()) > $validate->deadline)) return $this->build_return_json(0, [], '验证码不正确1');
                $member = new Member();
                $member->phone = $phone;
                $member->password = md5($password);
                $member->save();

                return $this->build_return_json(1, [], '注册成功');

            } else{
                return $this->build_return_json(0, [], '验证吗不正确2');
            }

        } return $this->build_return_json(0, [], '该手机号已被注册');



    }

}
