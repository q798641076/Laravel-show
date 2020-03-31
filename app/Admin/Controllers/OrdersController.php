<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Order';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);
        //只允许付过款的订单，按时间倒叙排序
        $grid->model()->whereNotNull('paid_at')->orderBy('created_at','desc');

        $grid->column('no', '订单流水号');
        $grid->column('user.name', '买家');

        $grid->column('total_amount','总价格')->sortable();

        $grid->ship_status('物流');

        $grid->refund_status('退款状态')->display(function($value) {
            return Order::$refundStatusMap[$value];
        });

        $grid->column('paid_at', '支付时间')->sortable();

        //禁止创建订单
        $grid->disableCreateButton();
        //禁止删除和编辑s
        $grid->actions(function($actions){
            $actions->disableEdit();
            $actions->disableDelete();
        });
        //辅助工具
        $grid->tools(function($tools){
            ////禁止批量删除
            $tools->batch(function($batch){
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
    //自定义 订单详情
    public function show($id,Content $content)
    {
        return $content
               ->header('查看订单')
               ->body(view('admin.orders.show',['order'=>Order::find($id)]));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', __('No'));
        $form->number('user_id', __('User id'));
        $form->textarea('address', __('Address'));
        $form->decimal('total_amount', __('Total amount'));
        $form->textarea('remark', __('Remark'));
        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', __('Payment method'));
        $form->text('payment_no', __('Payment no'));
        $form->text('refund_status', __('Refund status'))->default('pending');
        $form->text('refund_no', __('Refund no'));
        $form->switch('closed', __('Closed'));
        $form->switch('reviewed', __('Reviewed'));
        $form->text('ship_status', __('Ship status'))->default('pending');
        $form->textarea('ship_data', __('Ship data'));
        $form->textarea('extra', __('Extra'));

        return $form;
    }
}
