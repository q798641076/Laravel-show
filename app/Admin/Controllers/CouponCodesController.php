<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CouponCodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\CouponCode';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function index(Content $content)
    {
        return $content
                ->header('优惠卷管理')
                ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new CouponCode);

        $grid->model()->orderBy('created_at','desc');

        $grid->column('name', __('标题'));

        $grid->column('code', __('优惠卷码'));

        // $grid->column('type', __('类型'))->display(function($value){
        //     return CouponCode::$couponTypeMap[$value];
        // });

        // $grid->column('value', __('面值'))->display(function($value){
        //     return $this->type===CouponCode::TYPE_FIXED ? '￥'.$value : $value.'％';
        // });

        // $grid->column('min_amount', __('最低金额可用'));

        $grid->description('优惠面值');

        $grid->column('usage', __('用量'))->display(function($value){
            return $this->used.'/'.$this->total;
        });



        $grid->column('enabled', __('是否启用'))->display(function($value){
            return $value?'是':'否';
        });

        $grid->created_at('创建时间');

        $grid->actions(function($actions){
            //没有详情
            $actions->disableView();
        });

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
        $show = new Show(CouponCode::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('code', __('Code'));
        $show->field('type', __('Type'));
        $show->field('value', __('Value'));
        $show->field('total', __('Total'));
        $show->field('used', __('Used'));
        $show->field('min_amount', __('Min amount'));
        $show->field('not_before', __('Not before'));
        $show->field('not_after', __('Not after'));
        $show->field('enabled', __('Enabled'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CouponCode);

        $form->text('name', __('Name'));
        $form->text('code', __('Code'));
        $form->text('type', __('Type'));
        $form->decimal('value', __('Value'));
        $form->number('total', __('Total'));
        $form->number('used', __('Used'));
        $form->decimal('min_amount', __('Min amount'));
        $form->datetime('not_before', __('Not before'))->default(date('Y-m-d H:i:s'));
        $form->datetime('not_after', __('Not after'))->default(date('Y-m-d H:i:s'));
        $form->switch('enabled', __('Enabled'));

        return $form;
    }
}
