<?php

namespace App\Admin\Controllers;

use App\Events\OrderDelivered;
use App\Events\OrderRefund;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\OperatedRefundRequest;
use App\Http\Requests\Request;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Foundation\Validation\ValidatesRequests;
class OrdersController extends AdminController
{
    use ValidatesRequests;
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

        $grid->ship_status('物流状态')->display(function($value){
            return Order::$shipStatusMap[$value];
        });
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

    //发货控制器
    public function ship(Order $order,Request $request)
    {
        //如果订单未付款
        if(!$order->paid_at){
            throw new InvalidRequestException('订单未付款');
        }
        //如果订单已经发货了
        if($order->ship_status!==Order::SHIP_STATUS_PENDING){
            throw new InvalidRequestException('订单已发货');
        }
        //物流数据
        $data=$this->validate($request,[
            'express_company'=>'required',
            'express_no'=>'required'
        ],[],[
            'express_company'=>'物流公司',
            'express_no'=>'物流单号'
        ]);
        //更改订单数据
        $order->update([
            'ship_status'=>Order::SHIP_STATUS_DELIVERED,
            //物流数据是json 所以直接传递$data就好
            'ship_data'=>$data
        ]);
        //这里可以加一个发货通知
        event(new OrderDelivered($order));
        //返回上一层
        return back();
    }

    //退款申请
    public function oreatedRefund(OperatedRefundRequest $request,Order $order)
    {

        if(!$order->paid_at){
            throw new InvalidRequestException('订单未付款');
        }
        if($order->refund_status===Order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('退款状态不符');
        }

        //同意退款
        if($request->input('agree')){
            //清空拒绝退款的数据
            $extra=$order->extra ? : [];
            unset($extra['refusal_reason']);
            $order->update([
               'extra'=>$extra
            ]);
            //进行退款逻辑
            $this->_agreeRefund($order);
            //退款后通知
            event(new OrderRefund($order));
        }else{
            $extra=$order->extra ? :[];
            $extra['refusal_reason']=$request->input('reason');
            $order->update([
                'extra'=>$extra,
                'refund_status'=>Order::REFUND_STATUS_PENDING
            ]);
        }
        return [];
    }

    public function _agreeRefund(Order $order)
    {
        //判断支付方式
        switch($order->payment_method)
        {
            case 'wechar':

            break;

            case 'alipay':
            //退款单号
            $no=Order::refundAvailableNo();

            //调用refund方法，来执行退款
                $ret=app('alipay')->refund([
                    'out_trade_no'=>$order->no, //之前的订单流水号
                    'refund_amount'=>$order->total_amount, //退款金额
                    'out_request_no'=>$no   //退款流水号
                    ]);

                //根据支付宝的文档，如果返回值里有 sub_code 字段说明退款失败
                if($ret->sub_code){
                    //将退款失败保存到extra中
                    $extra=$order->extra;
                    $extra['refund_failed_code']=$ret->sub_code;
                    $order->update([
                        'refund_no'=>$no,
                        'refund_status'=>Order::REFUND_STATUS_FAILED,
                        'extra'=>$extra
                    ]);
                }else{
                     // 将订单的退款状态标记为退款成功并保存退款订单号
                    $order->update([
                        'refund_no'=>$no,
                        'refund_status'=>Order::REFUND_STATUS_SUCCESS
                    ]);
                }
            break;
            }

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
