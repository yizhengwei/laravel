<?php

namespace App\Admin\Controllers;

use App\Models\category;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Tree;


class CategoryController extends Controller
{
    use HasResourceActions;

    protected function tree()
    {
        return Category::tree(function (Tree $tree) {

            $tree->branch(function ($data) {
                return "{$data['id']} - {$data['name']}";

            });

        });
    }



    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {

        return Admin::content(function (Content $content) {

            $content->header('商品分类');
            $content->description('分类管理');
            $content->body($this->tree());
        });


    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new category);

        $grid->column('id', 'ID')->sortable();

        $grid->operation_id('operation_id');
        $grid->name('name')->setAttributes(['style' => 'color:red;']);;
        $grid->pid('pid');
        $grid->level('level');
        $grid->rank('rank');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(category::findOrFail($id));

        $show->id('ID');
        $show->operation_id('operation_id');
        $show->name('name');
        $show->pid('pid');
        $show->level('level');
        $show->rank('rank');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        return Admin::form(Category::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('pid', '所属类别')->options(Category::selectOptions());
            $form->text('name', '类别名称')->rules('required');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });

    }
}
