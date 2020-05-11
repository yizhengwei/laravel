<?php

namespace App\Http\Controllers\V1;

use App\Models\Cart;
use App\Models\Goods;
use App\Models\Order;
use App\Models\Sku;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    //
    public function getGoodsList()
    {
        $goods = Goods::where('operation_id', 1)->where('status', 1)->where('isShow', 1)->orderby('id')->get()->toArray();
        $goodsInfo = [];
        foreach ($goods as $good) {
            $a = Goods::where('operation_id', 1)->where('id', $good['id'])->first();

            $carts = Cart::where('operation_id', 0)->where('cid', 0)->where('goods_id', $a->id)->get();
            $sales_num= 0;
            foreach ($carts as $cart) {
                $sales_num += $cart->count;
            }

            $goodsInfo[] = [
                'id' => $good['id'],
                'goods_no' => $good['goods_no'],
                'goods_name' => $good['goods_name'],
                'tag_price' => $a->updateGoodsTagPrice(),
                'list_img' => $good['list_img'],
                'sales_num' => $sales_num,
                'content' => $good['content']
            ];
        }
        return $this->build_return_json(1, $goodsInfo, 'success');
    }

    public function detail(Request $request) {
        $id = $request->input('id');
        if (!$id) return $this->build_return_json(0, [], '缺少必要参数');
        $goods = Goods::where('operation_id', 1)->where('status', 1)->where('id', $id)->first();
        $skus = Sku::where('operation_id', 1)->where('goods_id', $goods->id)->get();

        $skuList = [];

        foreach ($skus as $sku) {
            $skuList[] = [
                'id' => $sku->id,
                'sales_price' => $sku->sales_price,
                'spec_name' => json_decode($sku->spec_list, true)['spec_val_name'],
                'stock' => $sku->stock
            ];
        }

        $goodsInfo = [
            'id' => $goods->id,
            'goods_no' => $goods->goods_no,
            'goods_name' => $goods->goods_name,
            'tag_price' => $goods->updateGoodsTagPrice(),
            'img_list' => json_decode($goods->img_list),
            'content' => $goods->content ?? '该商品暂无详情',
            'list_img' => $goods->list_img,
        ];

        return $this->build_return_json(1, ['goodsInfo' => $goodsInfo, 'skuList' => $skuList], 'success');
    }

    public function cateGoods(Request $request) {
        $cate_id = $request->input('cate_id');
        if (!$cate_id) return $this->build_return_json(0, [], '缺少必要参数');
        $goods = Goods::where('operation_id', 1)->where('class_second', $cate_id)->get();

        $data = [];
        foreach ($goods as $good) {


            $carts = Cart::where('operation_id', 0)->where('cid', 0)->where('goods_id', $good->id)->get();
            $sales_num= 0;
            foreach ($carts as $cart) {
                $sales_num += $cart->count;
            }
            $data[] = [
                'id' => $good->id,
                'goods_name' => $good->goods_name,
                'goods_no' => $good->goods_no,
                'tag_price' => $good->updateGoodsTagPrice(),
                'sales_num' => $sales_num ?? '',
                'list_img' => $good->list_img,
            ];
        }

        return $this->build_return_json(1, $data, 'success');

    }

    public function shopCart(Request $request) {
        $count = $request->input('count');
        $sku = $request->input('sku');
        $goods_id = $request->input('id');
        $phone = $request->input('phone');
        $sales_price = $request->input('sales_price');
        $spec_name = $request->input('spec_name');


        $cart = Cart::where('operation_id', 1)->where('goods_id', $goods_id)->first();


        if ($cart == null || $cart->spec_name != $spec_name) {
            $cart =  new Cart();
            $cart->goods_id = $goods_id;
            $cart->sku = json_encode($sku);
            $cart->count = $count;
            $cart->sales_price = $sales_price;
            $cart->spec_name = $spec_name;
//            $cart->phone = $phone;
            $cart->save();
        } else {
            $cart->count += $count;
            $cart->update();
        }

        return $this->build_return_json(1, [], '加入购物车成功');
    }

    public function getCartList() {
        $goods = Cart::where('operation_id', 1)->orderby('goods_id')->get();
        $data = [];
        foreach ($goods as $good) {
            $a = Goods::where('operation_id', 1)->where('id', $good->goods_id)->first();
            $data[] = [
                'id' => $a->id,
                'order_id' => $good->id,
                'goods_no' => $a->goods_no,
                'goods_name' => $a->goods_name,
                'count' => $good->count,
                'sku' => json_decode($good->sku, true),
                'sales_price' => $good->sales_price,
                'spec_name' => $good->spec_name,
                'list_img' => $a->list_img,
                'isChecked' => false
            ];
        }

        return $this->build_return_json(1, $data, 'success');
    }

    public function updateCount(Request $request){
        $id = $request->input('id');
        $count = $request->input('count');
        $cart = Cart::where('goods_id', $id)->first();
        if ($cart) {
            $cart->count = $count;
            $cart->update();
        }

        return $this->build_return_json(1, [], '更新成功');
    }

    public function delCart(Request $request) {
        $ids = $request->input('id');

        $ids = explode(',', $ids);
        $goods = Cart::where('operation_id', 1)->whereIn('goods_id', $ids)->get();
        foreach ($goods as $good) {
            $good->operation_id = 0;
            $good->save();
        }

        return $this->build_return_json(1, [], '成功');
    }

//    public function saveOrder(Request $request) {
//        $order_ids = $request->input('id');
//
//        $order_ids = explode(',', $order_ids);
//
//
//        //购物车s
//        $carts = Cart::whereIn('id', $order_ids)->get();
//
//
//        foreach ($carts as $cart) {
//
//            $order = Order::where('cart_id', $cart->id)->first();
//
//            if ($order == null) {
//                $order = new Order();
//                $order->order_no =  date("YmdHis", time()) . $cart->id;
//                $order->goods_id = $cart->goods_id;
//                $order->cart_id = $cart->id;
//                $order->sales_num = $cart->count;
//                $order->buy_time = date("Y-m-d H:i:s", time());
//                $order->save();
//            }
//            $cart->operation_id = 0;
//            $cart->save();
//        }
//
//        return $this->build_return_json(1, [], '购买成功');
//
//    }

    public function saveOrder(Request $request) {
        $ids = $request->input('id');
        $ids = explode(',', $ids);
        $ids = array_filter($ids);
        $order = new Order();
        $sales_price = 0;



        $carts = Cart::whereIn('id', $ids)->get();

        foreach ($carts as $cart) {
            $sales_price += $cart->sales_price;
            $cart->operation_id = 0;
            $cart->cid = 0;
            $cart->save();
        }

        $order->cart_id = json_encode($ids);
        $order->order_no =  date("YmdHis", time()). $order->id;
        $order->d = date("Y-m-d H:i:s", time());
        $order->buy_time = date("Y-m-d", time());
        $order->sales_num = $sales_price;
        $order->save();

        return $this->build_return_json(1, [], '购买成功');

    }





}

