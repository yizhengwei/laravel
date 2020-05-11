<?php

namespace App\Http\Controllers\Service;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Goods;
use App\Models\Sku;
use App\Models\Spec;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;




class GoodsController extends Controller
{




    public function getBrandList() {
        $list = Brand::where('operation_id', 1)->select('id', 'brand_name')->get();
        return $this->build_return_json(1, $list, 'success');
    }

    public function editView(Request $request) {
        $id = $request->input('id');
        if (!$id) return $this->build_return_json(0, [], 'success');
        $goods = Goods::where('operation_id', 1)->where('id', $id)->first();
        $data = [];
        if ($goods->count()) {
            $data['goods'] = [
                'id' => $goods->id,
                'goods_no' => $goods->goods_no,
                'goods_name' => $goods->goods_name,
                'cate_id' => $goods->getCateId(),
                'brand_id' => $goods->brand_id == 0 ? '' : $goods->brand_id,
                'sku_list' => $goods->getSkuList(),
                'list_img' => $goods->list_img,
                'content' => htmlspecialchars_decode($goods->content) ?? '',
                'status' => $goods->status,
                'isShow' => $goods->isShow,
                'img_list' => json_decode($goods->img_list),
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
        $skuList = $request->input('skuList');
        $list_img = $request->input('list_img');
        $isShow = $request->input('isShow');
        $bannerList = $request->input('bannerList');


        if ((preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $goods_no))) return $this->build_return_json(0, [], '商品编码不能为中文');
        if (!$goods_no || !$goods_name || !$skuList) return $this->build_return_json(0, [], '请填写必填项');

        $goods_by_no = Goods::where('operation_id', 1)->where('goods_no', $goods_no)->first();

        $flag = false;
        foreach ($skuList as $sku) {
            if ((preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $sku['sku_no']))) return $this->build_return_json(0, [], 'Sku编码不能为中文');
            if (empty($sku['tag_price']) || empty($sku['sales_price'])) return $this->build_return_json(0 , [], '销售价或吊牌价不能为空');
            $flag = true;
        }
        if (!$flag) return $this->build_return_json(0, [], '至少创建一条sku');

        DB::beginTransaction();
        try{
            if ($id) {
                $goods = Goods::where('operation_id', 1)->where('id', $id)->first();
                if (!$goods) return $this->build_return_json(0, [], '商品不存在');
                if ($goods_by_no && $goods_by_no->id != $id) return $this->build_return_json(0, [], '款号已存在');
            } else {
                if ($goods_by_no)  return $this->build_return_json(0, [], '商品编码已存在');
                $goods = new Goods();
                $goods->operation_id = 1;

            }
            $goods->brand_id = $brand_id;
            $goods->goods_no = $goods_no;
            $goods->goods_name = $goods_name;
            $goods->list_img = $list_img;
            $goods->isShow = $isShow;
            $goods->img_list = json_encode($bannerList);

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

            $hashSkus = Sku::where('operation_id', 1 )->where('goods_id', $goods->id)->get()->keyBy('sku_no');

            foreach($skuList as $sku) {
                if (!trim($sku['sku_no'])) continue;

                $goods_sku = $hashSkus[$sku['sku_no']] ?? null;


                if (!$goods_sku) {
                    $sku_by_no = Sku::where('operation_id', 1)->where('sku_no', $sku['sku_no'])->first();
                    if ($sku_by_no) return $this->build_return_json(0, [], 'sku编码已存在');
                    $goods_sku = new Sku();
                    $goods_sku->operation_id = 1;
                    $goods_sku->goods_id = $goods->id;
                    $goods_sku->goods_no = $goods->goods_no;
                    $goods_sku->sku_no = $sku['sku_no'];

                }

                $goods_sku->tag_price = $sku['tag_price'];
                $goods_sku->sales_price = $sku['sales_price'];
                $goods_sku->stock = $sku['stock'];

                $specList = [
                    'spec_id' => $sku['spec_id'],
                    'spec_name' => $sku['spec_name'],
                    'spec_val_id' => $sku['spec_val_id'],
                    'spec_val_name' => $sku['spec_val_name'],
                ];

                $goods_sku->spec_list = json_encode($specList);
                $goods_sku->save();
            }

            $a = $goods->updateGoodsStock();
            $goods->stock = $a;
            $goods->save();

            DB::commit();
            return $this->build_return_json(1, [], '操作成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->build_return_json(0, [], $e->getMessage());
        }
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

        $sql .= " ORDER BY id DESC limit {$limit} offset {$offset}";

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

    public function getGoodsSpec(Request $request) {
        $goodsSpecs = $request->input('goodsSpec');
        foreach ($goodsSpecs as $goodsSpec) {
            $spec_name = Spec::where('operation_id', 1)->where('id', $goodsSpec[0])->first()->name;
            $spec_val_name = Spec::where('operation_id', 1)->where('id', $goodsSpec[1])->first()->name;
            $data[] = [
                'spec_id' => $goodsSpec[0],
                'spec_name' => $spec_name,
                'spec_val_id' => $goodsSpec[1],
                'spec_val_name' => $spec_val_name,
            ];
        }
        return $this->build_return_json(1, $data, 'success');
    }

    public function delGoods(Request $request) {
        $id = $request->input('id');
        $goods = Goods::where('operation_id', 1)->where('id', $id)->first();
        $skus = Sku::where('operation_id', 1)->where('goods_id', $id)->get();
        if ($goods) {
            $goods->operation_id = 0;
            $goods->update();
        }
        if (count($skus) > 0) {
            foreach ($skus as $sku ) {
                $sku->goods_id = 0;
                $sku->operation_id = 0;
                $sku->update();
            }
        }
        return $this->build_return_json(1, [], '删除成功');
    }

    //批量删除
    public function delete(Request $request){
        $goods_nos = $request->input("goods_nos");
        if (empty($goods_nos)) return $this->build_return_json(0, [], '请选择删除商品');

        foreach ($goods_nos as $goods_no) {
            $goods = Goods::where('operation_id', 1)->where('goods_no', $goods_no)->first();

            if ($goods === null) continue;

            //删除sku表的内容
            $skus = Sku::where('operation_id', 1)->where('goods_id', $goods->id)->get();
            if (count($skus) > 0 ) {
                foreach ($skus as $sku) {
                    $sku->operation_id = 0;
                    $sku->goods_id = 0;
                    $sku->update();
                }
            }

            //删除goods表的内容
            $goods->operation_id = 0;
            $goods->save();

        }
        return $this->build_return_json(1, [], '批量删除成功');
    }

    public function curve() {
        $cates = Category::where('operation_id', 1)->where('pid', 0)->get();
        $data = [];
        foreach ($cates as $cate) {
            $data['x'][]= $cate->name;
            $goods = Goods::where('operation_id', 1)->where('class_first', $cate->id)->count();
            $data['value'][] = $goods;
        }

        return  $this->build_return_json(1, $data, 'success');

    }

}
