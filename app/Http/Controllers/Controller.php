<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function build_return_json($status = 0, $content = array(), $msg = '') {
        return response()->json(array('status' => $status, 'content' => $content, 'msg' => $msg));
    }

    public function toJson()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }


}
