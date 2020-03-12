<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class wxLoginController extends Controller
{
    //
    public function test(Request $request) {
        $code = $request->get('code');
        var_dump($code);die();
        return "#";
    }
}
