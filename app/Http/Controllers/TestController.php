<?php

namespace App\Http\Controllers;

use App\Tool\SMS\SendTemplateSMS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    //

    public function upload(Request $request)
    {
        if ($request->hasFile('file') and $request->file('file')->isValid()) {
            //数据验证
            $allow = array('image/jpeg', 'image/png', 'image/gif');

            $mine = $request->file('file')->getMimeType();
            if (!in_array($mine, $allow)) {
                return response()->json(['status' => 0, 'msg' => '文件类型错误，只能上传图片']);
            }

            //文件大小判断$filePath
            $max_size = 1024 * 1024 * 3;
            $size = $request->file('file')->getClientSize();
            if ($size > $max_size) {
                return response()->json(['status' => 0, 'msg' => '文件大小不能超过3M']);
            }

            //original图片,上传到本地
            $path = $request->file->store('/images');


            $file_path = public_path() . $path;
//            $file_path = storage_path('app/') . $path;


            return $path;

//            return "./storage/app/".$path;
//            return "/images/logo.png";
        }
    }

    public function sendSMS()
    {

        $sendTemplateSMS = new SendTemplateSMS;
        $code="";
        $charset="1234567890";
        $_len = strlen($charset) - 1;
        for ($i = 0;$i < 6;++$i) {
            $code .= $charset[mt_rand(0, $_len)];
        }

        $m3_result = $sendTemplateSMS->sendTemplateSMS("15858109256", array($code, 60), 1);

//        if($m3_result->status == 0){
//            $tempPhone = TempPhone::where('phone', $phone)->first();
//            if($tempPhone == null) {
//                $tempPhone = new TempPhone;
//            }
//            $tempPhone->phone = $phone;
//            $tempPhone->code = $code;
//            $tempPhone->deadline = date('Y-m-d H-i-s', time() + 60*60);
//            $tempPhone->save();
//        }
//
//        return $m3_result->toJson();
    }
}
