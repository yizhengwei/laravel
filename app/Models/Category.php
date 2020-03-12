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
}
