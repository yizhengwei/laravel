<?php

namespace App\Http\Controllers\Service;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    //

    public function getMemberList(Request $request) {

        $q = $request->input('q');
        $p = $request->input('p', 1);
        $limit = $request->input('limit', 10);
        $offset = ($p - 1) * $limit;

        $sql = "SELECT * FROM `member` WHERE operation_id = 1";

        $total = count( DB::select($sql));

        if ($q) {
            $sql .= " AND  phone like '%{$q}%'";
        }

        $sql .= " limit {$limit} offset {$offset}";


        $data = DB::select($sql);
        $data = json_encode($data, true);
        $data = json_decode($data,true);

        return $this->build_return_json(1, ['total' => $total, 'data' => $data], 'success');
    }
}
