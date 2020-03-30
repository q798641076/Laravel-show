<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order)
    {
        $this->authorize('own',$order);
        //如果订单已经支付或者被关闭
        if($order->paid_at || $order->closed){
            throw new InvalidRequestException('订单出现了问题');
        }

        //调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no'  =>$order->no,//订单编号
            'total_amount'  =>$order->total_amount,//订单金额，支持小数点后两位
            'subject'       =>'支付laravel Shop 的订单：'.$order->no//订单标题
        ]);
    }

    //浏览器回调
    public function alipayReturn()
    {
        // verify用于校验提交的参数是否合法
        try{
            app('alipay')->verify();
        }catch(\Exception $e){
            return view('pages.success',['msg'=>'数据不准确']);
        }
            return view('pages.success',['msg'=>'支付成功！']);
    }

    //支付宝服务器回调
    public function alipayNotify()
    {
        //校验输入参数
        $data=app('alipay')->verify();
        //如果订单状态不是成功或者失败，则返回数据给支付宝服务器，不再进行后续操作
        if(!in_array($data->trade_status,['TRADE_SUCCESS','TRADE_FINISHED'])){
            return app('alipay')->success();
        }
        //$data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order=Order::where('no',$data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if(!$order){
            return ;
        }
        //如果订单已经支付过了，就不继续操作
        if($order->paid_at){
            return app('alipay')->success();
        }
        $order->update([
            'paid_at'           =>Carbon::now(),  //支付时间
            'payment_method'    =>'alipay',       //支付方式
            'payment_no'        =>$data->trade_no //支付宝订单号
        ]);

        //触发订单事件
        $this->afterOrder($order);
        //如果最后不返回给支付宝服务器一个结果的话，那边会一直发送这笔订单的回调，直到我们返回为止
        return app('alipay')->success();
    }


    public function afterOrder($order)
    {
        event(new OrderPaid($order));
    }
}
