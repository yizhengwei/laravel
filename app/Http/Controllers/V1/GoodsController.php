<?php

namespace App\Http\Controllers\V1;

use App\Models\Goods;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{
    //
    public function getGoodsList()
    {
        $goods = Goods::where('operation_id', 1)->where('status', 1)->where('isShow', 1)->orderby('id')->get()->toArray();
        $goodsInfo = [];
        foreach ($goods as $good) {
            $a = Goods::where('operation_id', 1)->where('id', $good['id'])->first();

            $order = Order::where('goods_id', $good['id'])->first();
            if ($order == null) {
                $sales_num = 0;
            } else $sales_num = $order->sales_num;

            $goodsInfo[] = [
                'id' => $good['id'],
                'goods_no' => $good['goods_no'],
                'goods_name' => $good['goods_name'],
                'tag_price' => $a->updateGoodsTagPrice(),
                'list_img' => $good['list_img'],
                'sales_num' => $sales_num ?? ''
            ];
        }
        return $this->build_return_json(1, $goodsInfo, 'success');
    }

    public function detail(Request $request) {
        $id = $request->input('id');
        if (!$id) return $this->build_return_json(0, [], '缺少必要参数');
        $goods = Goods::where('operation_id', 1)->where('status', 1)->where('id', $id)->first();
        $goodsInfo = [
            'id' => $goods->id,
            'goods_no' => $goods->goods_no,
            'goods_name' => $goods->goods_name,
            'tag_price' => $goods->updateGoodsTagPrice(),
            'img_list' => json_decode($goods->img_list)
        ];

        return $this->build_return_json(1, $goodsInfo, 'success');
    }
}
