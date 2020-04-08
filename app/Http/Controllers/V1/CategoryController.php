<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    //
    public function getFirstCate() {
        $fisrtCate = Category::where('operation_id', 1)->where('pid', 0)->get();
        return $this->build_return_json(1, $fisrtCate, 'success');
    }

    public function getSecondCate(Request $request) {
        $pid = $request->input('pid');
        $secondCate = Category::where('operation_id', 1)->where('pid', $pid)->get();
        return $this->build_return_json(1, $secondCate, 'success');

    }
}
