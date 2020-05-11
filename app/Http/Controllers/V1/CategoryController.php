<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    //


    public function getCategoryList() {
        $cate_list = Category::where('operation_id', 1)
            ->where('pid', 0)
            ->select('id', 'name', 'pid')
            ->orderBy('rank', 'ASC')
            ->get()
            ->toArray();
        $data = [];
        if (count($cate_list) > 0) {
            foreach ($cate_list as $index => $value) {
                $data[$index] = $value;
                $data[$index]['list'] = Category::where('operation_id', 1)
                    ->where('pid', $value['id'])
                    ->select('id', 'name', 'pid', 'list_img')
                    ->orderBy('rank', 'ASC')
                    ->get()
                    ->toArray();
            }
        }

        return $this->build_return_json(1, $data, 'success');
    }



}
