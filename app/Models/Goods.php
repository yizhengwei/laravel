<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    //
    protected $table = 'goods';
    protected $primaryKey = 'id';
    public $timestamps = false;


    public function handleSku() {
        return Sku::where('goods_id', $this->id);
    }

    public function getTagPrice() {
        $price = 0;
        $sku_list = $this->handleSku()->select('tag_price')->get();
        if (!empty($sku_list)) {
            $total_price = array_sum(array_column($sku_list, 'tag_price'));
            $price = round($total_price / count($sku_list), 2);
        }
        return $price;
    }

    public function updateGoodsTagPrice() {

        $skus = Sku::where('operation_id', 1)->where('goods_id', $this->id)->get();

        $tag_price = 0;
        $count = 0;
        if ($skus->count()) {
            foreach ($skus as $sku) {
                $tag_price += $sku->tag_price;
                $count++;
            }
        }
        if ($count == 0 ){
            return 0;
        }
        return $tag_price/$count;

    }

}
