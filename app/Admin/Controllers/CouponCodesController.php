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
    // protected function detail($id)
    // {
    //     $show = new Show(CouponCode::findOrFail($id));

    //     $show->field('id', __('Id'));
    //     $show->field('name', __('Name'));
    //     $show->field('code', __('Code'));
    //     $show->field('type', __('Type'));
    //     $show->field('value', __('Value'));
    //     $show->field('total', __('Total'));
    //     $show->field('used', __('Used'));
    //     $show->field('min_amount', __('Min amount'));
    //     $show->field('not_before', __('Not before'));
    //     $show->field('not_after', __('Not after'));
    //     $show->field('enabled', __('Enabled'));
    //     $show->field('created_at', __('Created at'));
    //     $show->field('updated_at', __('Updated at'));

    //     return $show;
    // }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CouponCode);

        $form->display('id','ID');

        $form->text('name', __('标题'))->rules('required');

        $form->text('code', __('优惠码'))->rules(function(Form $form){
            //如果是编辑页面的话，id是存在的
            if($id=$form->model()->id){
                return 'nullable|unique:coupon_codes,code,'.$id;
            }
                return 'nullable|unqiue:coupon_codes';
        });
        //单选框要给默认值，避免不必要的报错
        $form->radio('type','类型')->options(CouponCode::$couponTypeMap)->default('TYPE_FIXED');

        $form->text('value','折扣')->rules(function(){
            if(request()->input('type')===CouponCode::TYPE_FIXED){
                return 'required|min:0.01|numeric';
            }
            return 'required|between:1,99|numeric';
        });

        $form->number('total', __('发放总数'))->rules('required|integer|min:1');

        $form->text('min_amount', __('最低金额'))->rules('required|numeric|min:1');

        $form->datetime('not_before', __('在这之后有效'));

        $form->datetime('not_after', __('在这之前有效'));

        $form->radio('enabled','是否生效')->options([1=>'是',0=>'否'])->default(1);

        $form->saving(function(Form $form){
            if(!$form->code){
                $form->code=CouponCode::findAvailableCode();
            }
        });

        return $form;
    }
}
