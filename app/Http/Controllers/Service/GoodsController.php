<?php

namespace App\Http\Controllers\Service;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Goods;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Service\CategoryController;


class GoodsController extends Controller
{
    //
    public function test() {

    }

    public function saveGoods(Request $request) {


    }

    public function getGoodsList(Request $request) {

        $p = $request->input('p',1);
        $limit = $request->input('limit', 10);
        $offset = ($p - 1) * $limit;

        $sql = "SELECT * FROM `goods` WHERE operation_id = 1";

        $cate_ids = $request->input('cate_ids') == "" ? [] : $request->input('cate_ids');

        $num = count($cate_ids);

        switch ($num) {
            case '1':
                $sql .= " AND class_first = {$cate_ids[0]}";
                break;
            case '2':
                $sql .= " AND class_second = {$cate_ids[1]}";
                break;
            case 3:
                $sql .= " AND class_third = {$cate_ids[2]}";
                break;
        }

        $list_img = $request->input('list_img');

        if ($list_img) {
            switch ($list_img) {
                case '1':
                    $sql .= " AND list_img <> '' ";
                    break;
                case '2':
                    $sql .= " AND list_img = '' ";
                    break;
            }
        }

        $goods_no = $request->input('goods_no');

        if ($goods_no) {
            $sql .= " AND (goods_name like '%{$goods_no}%' or goods_no like '%{$goods_no}%') ";
        }

        $sql .= " limit {$limit} offset {$offset}";

        $list = DB::select($sql);
        $list = json_encode($list, true);
        $list = json_decode($list,true);

        $total = count($list);

        $data = [];

        $data['total'] = $total;

        if (count($list) > 0 ) {
            foreach ($list as $value) {

                $goods = Goods::where('operation_id', 1)->Where('id', $value['id'])->first();
                $data['list'][] = [
                    'id' => $value['id'],
                    'goods_no' => $value['goods_no'],
                    'goods_name' => $value['goods_name'],
                    'img' => $value['list_img'],
                    'puton_time' => $value['puton_time'],
                    'status' => $value['status'],
                    'stock' => $value['stock'],
                    'avg_tag' =>$goods->updateGoodsTagPrice(),
                ];
            }
        } else {
            $data['list'] = [];
        }

        return $this->build_return_json(1, $data, 'success');
    }

    public function onSale(Request $request) {
        $goods_no = $request->input('goods_no');
        $status = $request->input('status');



        $goods = Goods::where('operation_id', 1)->where('goods_no', $goods_no)->first();

        if (!$goods) return $this->build_return_json(0, [], '无此商品');

        $goods->status = $status;

        if ($status == 1) {
            $goods->puton_time = date("Y-m-d H:i:s", time());
        }

        $goods->save();

        return $this->build_return_json(1, [], 'success');

    }

}
