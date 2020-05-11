<?php

namespace App\Exports;

use App\Models\Goods;
use Maatwebsite\Excel\Facades\Excel;
use MaatWebSite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection{
    public function collection()
    {
        return Goods::all();
    }
}
