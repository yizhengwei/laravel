<?php

namespace App\Http\Controllers\Service;

use App\Models\Cart;
use App\Models\Goods;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //

    public function getOrderList(Request $request) {

        $p = $request->input('p', 1);
        $limit = $request->input('limit', 10);
        $offset = ($p - 1) * $limit;

        $sql = "SELECT * FROM `order` ";

//        $total = count( DB::select($sql));

        $q = $request->input('q');

        if ($q) {
            $sql .= " WHERE order_no like  '%{$q}%' ";
        }

        $sql_count = "SELECT count(*) as num FROM ({$sql}) a ";

        $sql .= "limit {$limit} offset {$offset}";


        $total = DB::select($sql_count);
        $total = json_encode($total, true);
        $total = json_decode($total,true);
        $total = $total[0]['num'];

        $list = DB::select($sql);
        $list = json_encode($list, true);
        $list = json_decode($list,true);


        $data = [];
        foreach ($list as $value) {

            $cart_ids = json_decode($value['cart_id']);
            $sales_num = 0;
            foreach ($cart_ids as $cart_id) {
                $cart = Cart::where('id', $cart_id)->first();
                $sales_num += ($cart->sales_price * $cart->count);
                $good = Goods::where('id', $cart->goods_id)->first();
                $tmp[] = [
                    'cart_id' => $cart->id,
                    'spec_name' => $cart->spec_name,
                    'count' => $cart->count,
                    'list_img' => $good->list_img
                ];
            }

            $orders = [
                'id' => $value['id'],
                'order_no' => $value['order_no'],
                'buy_time' => $value['d'],
                'sales_num' => $sales_num."元",
//                'list' => $tmp
            ];

            $tmp =[];

            $data[] =$orders;
        }

       return $this->build_return_json(1, ['total' => $total, 'data' => $data], 'success');
    }


    public function curve() {

        $dateList = $this->getTime();
        $data = Order::where("buy_time",'>=', $dateList[0])
                    ->where('buy_time', '<=', $dateList[count($dateList) - 1])
                    ->select("id","sum(sales_num)")
                    ->get()->groupBy('buy_time')->toArray();



    }

    public function getTime () {
        $count = 0;
        $end = date('Y-m-d', time());
        $tmp = '';
        $dateList = [];
        while ( $count <30  ) {
            $tmp = date("Y-m-d H:i:s", strtotime("{$end} -{$count} days"));
            $dateList[] = $tmp;
            $count ++;
        }

        return array_reverse($dateList);
    }


    public function detail(Request $request) {
        $id = $request->input('id');
        if (!$id) return $this->build_return_json(0, [], '缺少必要参数');
        $order = Order::where('id', $id)->first();
        $cart_ids = json_decode($order->cart_id);
        $data = [];
        foreach ($cart_ids as $cart_id) {
            $cart = Cart::where('cid', 0)->where('operation_id', 0)->where('id', $cart_id)->first();
            if ($cart) {
                $goods = Goods::where('id', $cart->goods_id)->first();
                $data[] = [
                    'id' => $cart->id,
                    'goods_no' => $goods->goods_no,
                    'goods_name' => $goods->goods_name,
                    'list_img' => $goods->list_img,
                    'count' => $cart->count,
                    'spec_name' => $cart->spec_name,
                    'sales_num' => $cart->sales_price,
                    'd' => $order->d,
                ];
            }
        }
        return $this->build_return_json(1, $data, 'success');
    }
}
