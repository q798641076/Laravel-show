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
     */
    // protected function form()
    // {
    //     $form = new Form(new Product);

    //     $form->number('user_id', __('User id'));
    //     $form->text('title', __('Title'));
    //     $form->textarea('description', __('Description'));
    //     $form->image('image', __('Image'));
    //     $form->switch('on_sale', __('On sale'))->default(1);
    //     $form->decimal('rating', __('Rating'))->default(5.00);
    //     $form->number('sold_count', __('Sold count'));
    //     $form->number('review_count', __('Review count'));
    //     $form->decimal('price', __('Price'));

    //     return $form;
    // }
}
