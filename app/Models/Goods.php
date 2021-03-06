<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    //
    protected $table = 'goods';
    protected $primaryKey = 'id';
    public $timestamps = false;
    /**
     * @var int
     */
//    private $operation_id;
//    private $brand_id;
//    private $goods_no;
//    private $goods_name;
//    private $class_first;
//    private $class_second;
//    private $class_third;


    public function getCateId () {
    $cates = Goods::where('id', $this->id)
        ->select('class_first', 'class_second', 'class_third')
        ->get()->toArray();

    foreach ($cates as $cate) {
        $arr = array_filter($cate);
    }

    return array_values($arr);
}

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

    public function updateGoodsStock() {

        $stock = $this->handleSku()->select('stock')->get()->sum('stock');
//
        return $stock;
    }

    public function getSkuList() {
        $list = [];
        $sku_list = $this->handleSku()
            ->select('id', 'operation_id', 'goods_id', 'sku_no', 'tag_price', 'sales_price', 'spec_list', 'stock')
            ->get()->toArray();
        if (count($sku_list) > 0) {
            foreach ($sku_list as $index => $sku) {
                $specList = json_decode($sku['spec_list'], true);
                $list[$index] = [
                    'id' => $sku['id'],
                    'sku_no' => $sku['sku_no'],
                    'tag_price' => $sku['tag_price'],
                    'sales_price' => $sku['sales_price'],
                    'spec_id' => $specList['spec_id'],
                    'spec_name' => $specList['spec_name'],
                    'spec_val_id' => $specList['spec_val_id'],
                    'spec_val_name' => $specList['spec_val_name'],
                    'stock' => $sku['stock'],

                ];
            }
        }
        return $list;
    }

}
