<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller{
    public function toCategory() {
        $categorys = Category::where('pid', 0)->get();
        return view("category")->with('categorys', $categorys);
    }
}
