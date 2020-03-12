<?php

namespace App\Http\Controllers\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Menu;

class MenuController extends Controller
{
    //

    public function getMenuList() {

        $arr = [];
        $firstMenu = Menu::where('pid', 0)->orderBy('rank','ASC')->get()->toArray();
        if (!empty($firstMenu)) {
            foreach ($firstMenu as $value) {
                $value['list'] = Menu::where('pid', $value['id'])->get()->toArray();
                $arr[] = $value;
            }
        }
        return $this->build_return_json(1, $arr, 'success');
    }

}
