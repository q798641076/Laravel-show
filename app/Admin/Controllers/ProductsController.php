<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        //商品描述 商品图片 关联用户，这里没有设置
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();

        $grid->title('商品名称');

        $grid->on_sale('已上架')->display(function($value){
            return $value ? '是':'否';
        });

        $grid->rating('评分');

        $grid->sold_count('售出总数');

        $grid->review_count('评论总数');

        $grid->price('价格');

        $grid->actions(function($action){
            $action->disableView();
            $action->disableDelete();
        });

        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });



        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    // protected function detail($id)
    // {
    //     $show = new Show(Product::findOrFail($id));

    //     $show->field('id', __('Id'));
    //     $show->field('user_id', __('User id'));
    //     $show->field('title', __('Title'));
    //     $show->field('description', __('Description'));
    //     $show->field('image', __('Image'));
    //     $show->field('on_sale', __('On sale'));
    //     $show->field('rating', __('Rating'));
    //     $show->field('sold_count', __('Sold count'));
    //     $show->field('review_count', __('Review count'));
    //     $show->field('price', __('Price'));
    //     $show->field('created_at', __('Created at'));
    //     $show->field('updated_at', __('Updated at'));

    //     return $show;
    // }

    /**
     * Make a form builder.
     *
     * @return Form
     * 新增和编辑
     */
    protected function form()
    {
        $form = new Form(new Product);


        $form->text('title', __('商品名称'))->rules('required|min:2');

        //富文本编辑器
        $form->quill('description', __('商品描述'))->rules('required');

        $form->image('image', __('商品图片'))->rules('required|image');

        //单选框
        $form->radio('on_sale', __('是否上架'))->options(['1'=>'是','0'=>'否'])->default(0);

        //直接添加一个一对多关联模型
        //第一个参数是该模型关联的属性
        $form->hasMany('skus','SKU 列表', function(Form\NestedForm $form){
            $form->text('title','sku名称')->rules('required');
            $form->text('description','sku描述')->rules('required|min:2');
            $form->text('price','单价')->rules('required|numeric|min:0.01');
            $form->text('stock','剩余库存')->rules('required|integer|min:0');
        });

        //定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function(Form $form){
            $form->model()->price=collect($form->skus)->min('price') ? :0;
        });

        return $form;
    }
}
