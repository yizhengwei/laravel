<?php

namespace App\Http\Controllers\Service;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Goods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;




class GoodsController extends Controller
{
    //
    public function getBrandList() {
        $list = Brand::where('operation_id', 1)->select('id', 'brand_name')->get();
        return $this->build_return_json(1, $list, 'success');
    }

    public function editView(Request $request) {
        $id = $request->input('id');
        $goods = Goods::where('operation_id', 1)->where('id', $id)->first();
        $data = [];
        if ($goods->count()) {
            $data['goods'] = [
                'id' => $goods->id,
                'goods_no' => $goods->goods_no,
                'goods_name' => $goods->goods_name,
                'cate_id' => $goods->getCateId(),
                'brand_id' => $goods->brand_id == 0 ? '' : $goods->brand_id,
//                'sku_list' => $goods->getSkuList(),
                'list_img' => $goods->list_img,
                'content' => htmlspecialchars_decode($goods->content) ?? '',
                'status' => $goods->status,
            ];
        }
        return $this->build_return_json(1, $data, 'success');

    }

    public function saveGoods(Request $request)
    {
        $id = $request->input('id');
        $goods_no = $request->input('goods_no');
        $goods_name = $request->input('goods_name');
        $cate_id = $request->input('cate_id');
        $brand_id = $request->input('brand_id');
        $content = $request->input('content');
        $status = $request->input('status');

//        if ((preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $goods_no))) return $this->build_return_json(0, [], '商品编码不能为中文');
        if (!$goods_no || !$goods_name) return $this->build_return_json(0, [], '请填写必填项');

        $goods_by_no = Goods::where('operation_id', 1)->where('goods_no', $goods_no)->first();


        if ($id) {

        } else {
            if ($goods_by_no)  return $this->build_return_json(0, [], '商品编码已存在');
            $goods = new Goods();
            $goods->operation_id = 1;
            $goods->brand_id = $brand_id;
            $goods->goods_no = $goods_no;
            $goods->goods_name = $goods_name;

            if ($cate_id) {
                $cate = Category::where('operation_id', 1)->where('id', $cate_id)->first();
                $pid = $cate->pid;
                $list = [];
                $list[] = $cate->id;
                while($pid != 0 || empty($cate)) {
                    $p_cate = Category::where('operation_id', 1)->where('id', $pid)->first();
                    $pid = $p_cate->pid;
                    $list[] = $p_cate->id;
                }
                $array = array_reverse($list);
                $goods->class_first = $array[0];
                $goods->class_second = $array[1];
                $goods->class_third = $array[2];

                $goods->content = $content;
                if ($status == 1) {
                    $goods->puton_time =  date("Y-m-d H:i:s", time());
                }
                $goods->status = $status;

            }

            $goods->save();

        }

        return $this->build_return_json(1, [], '操作成功');
    }

    public function getGoodsList(Request $request) {

        $p = $request->input('p',1);
        $limit = $request->input('limit', 10);
        $offset = ($p - 1) * $limit;

        $sql = "SELECT * FROM `goods` WHERE operation_id = 1";

        $total = count( DB::select($sql));

        $brand_id = $request->input('brand');
        if ($brand_id) {
            $sql .= " AND brand_id = {$brand_id}";
        }

        $cate_ids = $request->input('cate_id') == "" ? [] : $request->input('cate_id');

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

    //上下架  暂时只有一个商品
    public function onSale(Request $request) {
        $goods_no = $request->input('goods_no');
        $status = $request->input('status');

        $goods = Goods::where('operation_id', 1)->where('goods_no', $goods_no)->first();
        if (!$goods) return $this->build_return_json(0, [], '无此商品');

        if ($status == 1) {
            $goods->puton_time = date("Y-m-d H:i:s", time());
        }
        $goods->status = !$status;
        $goods->save();

        return $this->build_return_json(1, [], 'success');
    }



}
