<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Spec;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    //获取分类列表
    public function getCategoryList1() {
        $data= $this->tree();
        return $this->build_return_json(1, $data, "success");
    }

    public function tree($parent_id = 0){
        $rows = Category::where('operation_id', ">", '0')->where('pid', $parent_id)->orderBy('rank','ASC')->get()->toArray();

        $arr = [];
        if (sizeof($rows) != 0){
            foreach ($rows as $key => $val){
                $val['list'] = $this->tree($val['id']);
                $arr[] = $val;
            }
            return $arr;
        }
    }

    public function getCategoryList(Request $request) {
        $type = $request->input('type');
        $data = [];
        switch ($type) {
            case 2:
                $cate_list = Category::where('operation_id', 1)
                    ->where('pid', 0)
                    ->orderBy('rank', 'ASC')
                    ->get()
                    ->toArray();
                if (count($cate_list) > 0) {
                    foreach ($cate_list as $index => $value) {
                        $data[$index] = $value;
                        $data[$index]['list'] = Category::where('operation_id', 1)
                            ->where('pid', $value['id'])
                            ->orderBy('rank', 'ASC')
                            ->get()
                            ->toArray();
                    }
                }
                break;

            default:

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

                break;
        }


        return $this->build_return_json(1, $data, 'success');
    }

    //新增编辑分类
    public function saveCategory(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');

        if (!$name || mb_strlen($name) > 10) return $this->build_return_json(0, [], '分类名称不能为空或长度不能多于10个字符');

        if ($id) {
            $pid = $request->input('pid');
            $cate = Category::where('operation_id', 1)->where('id', $id)->first();
            if (!$cate)  return $this->build_return_json(0, [], '数据错误');
            if ($cate->name != $name) {
                $cate_by_name = Category::where('name', $name)->where('pid', $pid)->first();
                if ($cate_by_name) return $this->build_return_json(0, [], '统一分类下名称不能重复');
            }
        } else {
            $pid = $request->input('pid');
            $cate_by_name = Category::where('name', $name)->where('pid', $pid)->first();
            if ($cate_by_name) return $this->build_return_json(0, [], '同一分类下名字不能重复');
            $cate = new Category();

        }
        $cate->pid  = $pid;
        $cate->level =  Category::getLevel($pid);
        $cate->name = $name;
        $cate->operation_id = "1";
        $cate->save();

        if (!$cate->rank){
            $cate->rank = $cate->id;
            $cate->update();
        }
        return $this->build_return_json(1, [], "操作成功");
    }

    //删除分类
    public function delCategory(Request $request) {
        $id = $request->input('id');
        $cate = Category::where('id', $id)->first();
        if (!$cate) return $this->build_return_json(0, [], '数据错误');

        $cate->operation_id = "0";
        $cate->pid  = "0";
        $cate->update();

        return $this->build_return_json(1, [], '操作成功');
    }

    //上移下移分类
    public function sortCategory(Request $request) {

        $id = $request->input('id');
        $cate = Category::where('operation_id', 1)->where('id', $id)->first();
        if (empty($cate)) return $this->build_return_json(0, [], '数据不存在');

        $type = $request->input('type','up');
        if (!in_array($type, ['up', 'down'])) return $this->build_return_json(0, [], '非法操作');

        if ($type == 'up') {
            $pre_cate = Category::where('operation_id', 1)
                ->where('pid', $cate->pid)
                ->where('rank', '<', $cate->rank)
                ->orderBy('rank','DESC')
                ->first();
        } else {
            $pre_cate = Category::where('operation_id', 1)
                ->where('pid', $cate->pid)
                ->where('rank', '>', $cate->rank)
                ->orderBy('rank','ASC')
                ->first();
        }

        if (empty($pre_cate)) return $this->build_return_json(1, [], 'success');


        $pre_cate_rank = $pre_cate->rank;
        $cate_rank = $cate->rank;

        $cate->rank = $pre_cate_rank;
        $pre_cate->rank = $cate_rank;

        $cate->update();
        $pre_cate->update();

        return $this->build_return_json(1, [] , 'success');
    }

    public function getSpecList() {
        $oneSpecs = Spec::where('operation_id', 1)
            ->where('pid', 0)
            ->get();
        $data = [];
        if (count($oneSpecs) > 0 ) {
            foreach ($oneSpecs as $index => $oneSpec) {
                $data[$index] = $oneSpec;
                $data[$index]['list'] = Spec::where('operation_id', 1)
                    ->where('pid', $oneSpec->id)
                    ->orderBy('rank','ASC')
                    ->get()
                    ->toArray();
            }
        }
        return $this->build_return_json(1, $data, 'success');
    }

    public function saveSpec(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');

        if (!$name || mb_strlen($name) > 10) return $this->build_return_json(0, [], '规格值不能为空或长度不能超过10');

        if ($id) {
            $pid = $request->input('pid');
            $spec = Spec::where('operation_id', 1)->where('id', $id)->first();
            if (!$spec) return $this->build_return_json(0, [], '规格值不存在');
            if ($spec->name != $name) {
                $spec_by_name = Spec::where('operation_id', 1)->where('name', $name)->where('pid', $pid)->first();
                if ($spec_by_name) return $this->build_return_json(0, [], '统一分类下名称不能重复');
            }
        } else {
            $pid = $request->input('pid');
            $spec_by_name = Spec::where('operation_id', 1)->where('name', $name)->where('pid', $pid)->first();
            if ($spec_by_name) return $this->build_return_json(0, [], '同一分类下名字不能重复');
            $spec = new Spec();
        }

        $spec->pid  = $pid;
        $spec->name = $name;
        $spec->operation_id = "1";
        $spec->save();

        if (!$spec->rank){
            $spec->rank = $spec->id;
            $spec->update();
        }

        return $this->build_return_json(1, [], "操作成功");

    }

    //删除规格
    public function delSpec(Request $request) {
        $id = $request->input('id');
        $spec = Spec::where('id', $id)->first();
        if (!$spec) return $this->build_return_json(0, [], '数据错误');

        $spec->operation_id = "0";
        $spec->pid  = "0";
        $spec->update();

        return $this->build_return_json(1, [], '操作成功');
    }

    public function sortSpec(Request $request) {
        $id = $request->input('id');
        $spec = Spec::where('operation_id', 1)->where('id', $id)->first();
        if (empty($spec)) return $this->build_return_json(0, [], '数据不存在');

        $type = $request->input('type', 'up');
        if (!in_array($type,['up', 'down'])) return $this->build_return_json(0, [], '非法操作');

        if ($type == 'up') {
            $pre_spec = Spec::where('operation_id', 1)
                ->where('pid', $spec->pid)
                ->where('rank', '<', $spec->rank)
                ->orderBy('rank', 'DESC')
                ->first();

        } else {
            $pre_spec = Spec::where('operation_id', 1)
                ->where('pid', $spec->pid)
                ->where('rank', '>', $spec->rank)
                ->orderBy('rank', 'ASC')
                ->first();
        }
        if (!$pre_spec) return $this->build_return_json(1, [], 'success');
        $pre_spec_rank = $pre_spec->rank;
        $spec_rank = $spec->rank;

        $spec->rank = $pre_spec_rank;
        $pre_spec->rank = $spec_rank;

        $spec->update();
        $pre_spec->update();

        return $this->build_return_json(1, [], '操作成功');

    }


}





