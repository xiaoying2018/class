<?php

if( !function_exists('str2array') ){

    function str2array($str,$delimiter=',')
    {
        if( !is_string($str) )  return false;
        if( $str === "" )   return [];
        return explode($delimiter, $str);
    }
}

if( !function_exists('exists_key') ){
    /**
     * @param $key
     * @param $array
     * @param int $mode 1.严谨模式 2.模糊模式
     * @return bool
     */
    function exists_key ($key, $array, $mode=1)
    {
        // 验证key合法度
        if( !is_array( $array ) )
            return false;
        $result             =   false;
        if(is_string($key)){
            switch ($mode){
                case 1:
                    $result =   array_key_exists($key,$array) && $array[$key];
                    break;
                case 2:
                    $result =   array_key_exists($key,$array);
                    break;
            }
        }
        return (boolean)$result;
    }
}

if( !function_exists('pattern') ){
    /**
     * @param $pattern
     * @param $subject
     * @param array|null $matches
     * @param int $flags
     * @param int $offset
     * @return int
     */
    function pattern ($pattern, $subject,array &$matches=null, $flags = 0, $offset = 0  )
    {
        return preg_match( $pattern, $subject,$matches, $flags, $offset );
    }
}

if (!function_exists('mailsend')) {
    /**
     * @param $to
     * @param $title
     * @param $content
     * @param string $back
     * @return array
     * use    mailsend('jialongfeicn@gmail.com','测试','测试')
     */
    function mailsend($to, $title, $content, $back='')
    {

        require('./ThinkPHP/Library/Vendor/phpmailer/class.phpmailer.php');
        try {
            $mail = new \PHPMailer(true);
            $mail->IsSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true; // 开启认证
            $mail->Port = 465;    // 网易为25
            $mail->Host = "smtp.163.com";
            $mail->Username = "zgs5999@163.com";    //qq此处为邮箱前缀名
            $mail->Password = "laozhou616888"; //开启qq邮箱SMTP服务时获得
            if ($back)// 回复地址
            {
                $mail->AddReplyTo($back, "小莺出国");
            }else{
                $mail->AddReplyTo("zgs5999@163.com", "小莺出国");//回复地址
            }
            $mail->From = "zgs5999@163.com";
            $mail->FromName = '小莺出国';
            $mail->AddAddress($to);
            $mail->Subject = $title;
            $mail->Body = $content;
            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示
            $mail->WordWrap = 80; // 设置每行字符串的长度
//$mail->AddAttachment("f:/test.png"); // 添加附件
            $mail->IsHTML(true);
            $mail->Send();
            return ['status'=>true,'msg'=>'success'];
        } catch (phpmailerException $e) {
            return ['status'=>false,'msg'=>$e->errorMessage()];
        }
    }

}

if( !function_exists('check_email') ){
    /**
     * @param $email
     * @return int
     */
    function check_email($email)
    {
        // Create the syntactical validation regular expression
        $regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";

        // Presume that the email is invalid
        $valid = 0;

        // Validate the syntax
        if (eregi($regexp, $email))
        {
            list($username,$domaintld) = split("@",$email);
            // Validate the domain
            if (getmxrr($domaintld,$mxrecords))
                $valid = 1;
        } else {
            $valid = 0;
        }

        return $valid;
    }
}

function test_jing()
{
    echo 1;
}
/**
 * 通过curl发送http post 请求
 *  - $header 数组形式 $header=['Content-Type:text/json','Authorization:xxxxxxx'];
 *  - $data 数组形式 $data=['param'=>$value];
 * @param $url string
 * @param $header array
 * @param $data array
 * @return array
 */
function curlPost($url,$header,$data){
    try{
        $ch = curl_init();

        if (substr($url, 0, 5) == 'https') {
            // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);// 设置请求的url
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);// 设置请求的HTTP Header
        // 设置允许查看请求头信息
        // curl_setopt($ch,CURLINFO_HEADER_OUT,true);
        curl_setopt($ch, CURLOPT_POST, true);// 请求方式是POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));// 设置发送的data
        $response = curl_exec($ch);
        // 查看请求头信息
        // dump(curl_getinfo($ch,CURLINFO_HEADER_OUT));
        if ($error = curl_error($ch)) {
            // 如果发生错误返回错误信息
            curl_close($ch);
            $ret=['status'=>false,'msg'=>$error];
            return $ret;
        } else {
            // 如果发生正确则返回response
            curl_close($ch);
            $ret=['status'=>true,'msg'=>$response];
            return $ret;
        }
    }catch (\Exception $exception){
        $ret=['status'=>false,'msg'=>$exception->getMessage()];
        return $ret;
    }
}

/**
 * 发送http POST请求 部分内容需要发送文件
 *  - 发送文件中 CURLOPT_POSTFIELDS  没有使用 http_build_query()
 *  - 如果只是发送数据请求不传送文件，使用 http_build_query()可以减少发送请求数据包大小
 *  $data 数据构造 $data['fileParam'=>curl_file_create($path,'image/jpeg'),'fileParam2'=>curl_file_create($path,'image/jpeg')]
 *      - path 必须是绝对路径，如果不是绝对路径必须使用 realpath($path)使用
 * @param $url
 * @param $header
 * @param $data
 * @return array
 */
function curlPostWithFile($url,$header,$data){
    try{
        $ch = curl_init();
        if (substr($url, 0, 5) == 'https') {
            // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);// 设置请求的url
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);// 设置请求的HTTP Header
        // 设置允许查看请求头信息
        // curl_setopt($ch,CURLINFO_HEADER_OUT,true);
        curl_setopt($ch, CURLOPT_POST, true);// 请求方式是POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);// 设置发送的data 使用的 multipart/form-data
        $response = curl_exec($ch);
        // 查看请求头信息
        // dump(curl_getinfo($ch,CURLINFO_HEADER_OUT));
        if ($error = curl_error($ch)) {
            // 如果发生错误返回错误信息
            curl_close($ch);
            $ret=['status'=>false,'msg'=>$error];
            return $ret;
        } else {
            // 如果发生正确则返回response
            curl_close($ch);
            $ret=['status'=>true,'msg'=>$response];
            return $ret;
        }
    }catch (\Exception $exception){
        $ret=['status'=>false,'msg'=>$exception->getMessage()];
        return $ret;
    }
}

/**
 * 发送http get请求
 * @param $url
 * @param $header
 * @param $data
 * @return array
 */
function curlGet($url,$header,$data){
    try{
        $ch = curl_init();
        if (substr($url, 0, 5) == 'https') {
            // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);// 设置请求的url
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);// 设置请求的HTTP Header
        // 设置允许查看请求头信息
        // curl_setopt($ch,CURLINFO_HEADER_OUT,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));// 设置发送的data 使用的 multipart/form-data
        $response = curl_exec($ch);
        // 查看请求头信息
        // dump(curl_getinfo($ch,CURLINFO_HEADER_OUT));
        if ($error = curl_error($ch)) {
            // 如果发生错误返回错误信息
            curl_close($ch);
            $ret=['status'=>false,'msg'=>$error];
            return $ret;
        } else {
            // 如果发生正确则返回response
            curl_close($ch);
            $ret=['status'=>true,'msg'=>$response];
            return $ret;
        }
    }catch (\Exception $exception){
        $ret=['status'=>false,'msg'=>$exception->getMessage()];
        return $ret;
    }
}

    /**
     * 跳向支付宝付款
     * @param  array $order 订单数据 必须包含 out_trade_no(订单号)、price(订单金额)、subject(商品名称标题)
     * @throws Exception
     */
    function alipay($order){
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = trim($order['out_trade_no']);
        //订单名称，必填
        $subject = trim($order['subject']);
        //付款金额，必填
        $total_amount = trim($order['total_amount']);
        //商品描述，可空
        //$body = trim($_POST['WIDbody']);
        $config = C('alipay');
        Vendor('Alipay.pagepay.service.AlipayTradeService');
        Vendor('Alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder');
        //构造参数
        $payRequestBuilder = new AlipayTradePagePayContentBuilder();
        //$payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $aop = new AlipayTradeService($config);
        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        //输出表单
        var_dump($response);
    }

    /**
     * 用于前端回调
     */
    function alipayReturn()
    {
        $config = C('alipay');
        Vendor('Alipay.pagepay.service.AlipayTradeService');
        $arr=$_GET;
        unset($arr['m']);
        $alipaySevice = new \AlipayTradeService($config);
        return $result = $alipaySevice->check($arr);
    }


    /**
     *  用户后端回调
     */
    function alipayNotify()
    {
        $config = C('alipay');
        Vendor('Alipay.pagepay.service.AlipayTradeService');
        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService($config);
        return $result = $alipaySevice->check($arr);
    }


    /**
     * 生成二维码
     * @param  string  $url  url连接
     * @param  integer $size 尺寸 纯数字
     */
    function qrcode($url,$size=4){
        Vendor('Phpqrcode.phpqrcode');
        QRcode::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
    }

    /**
     * 微信扫码支付
     * @param  array $order 订单 必须包含支付所需要的参数 body(产品描述)、total_fee(订单金额)、out_trade_no(订单号)、product_id(产品id)
     */
    function wechatPay($order){
        $order['trade_type']='NATIVE';
        Vendor('Weixinpay.Weixinpay');
        $weixinpay=new \Weixinpay();
        $weixinpay->pay($order);
    }


    /**
     * notify_url接收页面
     */
    function wechatNotify(){
        // 导入微信支付sdk
        Vendor('Weixinpay.Weixinpay');
        $wxpay=new \Weixinpay();
        return $wxpay->notify();
    }


    //传递数据以易于阅读的样式格式化后输出
    function p($data){
        // 定义样式
        $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
        // 如果是boolean或者null直接显示文字；否则print
        if (is_bool($data)) {
            $show_data=$data ? 'true' : 'false';
        }elseif (is_null($data)) {
            $show_data='null';
        }else{
            $show_data=print_r($data,true);
        }
        $str.=$show_data;
        $str.='</pre>';
        echo $str;
    }

    /**
     * 获取客户端IP
     * @return array|false|string
     */
    function getClientIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }

