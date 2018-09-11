<?php
namespace Home\Controller;

use Think\Controller;

class PaymentController extends Controller
{

    /**      * 电脑端唤醒 支付宝扫码支付接口      */
    public function aliPayPage()
    {
       
        $order =[
            'out_trade_no'=>'OD'.time(),
            'subject'=>'订单-测试',
            'total_amount'=>'1000',
        ];


       alipay($order);
    }

    // 前端回调页面
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        if(alipayReturn($_GET)){
            echo '验证成功';
        }else{
            echo '验证失败';
        }
    }


    public function aliPayNotify()
    {
        if(aliPayNotify()){
            echo '验证成功';
            echo 'success';
        }else{
            echo '验证失败';
            echo 'fail';
        }
    }

    public function qrcode()
    {
        $url ='http://www.baidu.com';
        qrcode($url);
    }



}