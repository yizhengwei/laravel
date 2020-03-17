<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'id';

    public static function getLevel ($pid) {
        if ($pid == 0) return 1;
        $level = 1;
        while ($pid != 0) {
            $level++;
            $cate = Category::where("id", $pid)->first();
            $pid = $cate->pid;
        }
        return $level;
    }

    public function getCateList() {
        $oneCates = Category::where('operation_id', 1)
            ->where('pid', 0)
            ->orderBy('rank', 'ASC')
            ->get()
            ->toArray();
        $data = [];
        if (sizeof($oneCates) != 0){
            foreach ($oneCates as $index => $oneCate) {
                $data[$index] = $oneCate;
                $secondCates = Category::where('operation_id', 1)
                    ->where('pid', $oneCate['id'])
                    ->orderBy('rank', 'ASC')
                    ->get()
                    ->toArray();
                $data[$index]['list'] = [];

                if (sizeof($secondCates) != 0){
                    foreach ($secondCates as  $secondCate) {
                        $tmp = $secondCate;
                        $tmp['list'] = Category::where('operation_id', 1)
                            ->where('pid', $secondCate['id'])
                            ->orderBy('rank', 'ASC')
                            ->get()
                            ->toArray();
                        $data[$index]['list'][] = $tmp;

                    }
                }
            }
        }
        return $data;
    }
}
