<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/9
 * Time: 10:45
 */

namespace Home\Controller;


use Think\Controller;

class BindmailController extends Controller
{
    /**
     * 发送激活邮箱的邮件
     */
    public function bindmail()
    {
        $email = I('post.email');

        if ( !$email || !check_email($email) ) $this->ajaxReturn(['result'=>false,'msg'=>'unknown email type.']);// 邮箱格式不正确

//        $email_login_addr = 'https://mail.'.trim(strrchr($email, '@'),'@');// 邮箱登录地址 暂不需要

        $condition = M('Students')->where(['email'=>['eq',$email]])->select();// 获取用户信息

        if ($condition)// 邮箱已存在
        {
            $this->ajaxReturn(['result'=>false,'msg'=>'该邮箱已被绑定']);
        }

        $rand_str = md5(time().'xiaoying');// 随机激活码
        $stu = session('_student.id');// 用户编号
        $ac = $email;// 当前用户的邮箱
        $code = md5('xiao'.time().'ying');// 无用参数

        // r = randstr随机激活码 s = student用户 ac|code = 无用的混淆参数
//        $content = '点此链接: '.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/Home/Bindmail/checkmail?r='.$rand_str.'&amp;c='.$ac.'&amp;s='.$stu.'&amp;code='.$code.' 激活邮箱.';
        $content = 'http://'.$_SERVER['HTTP_HOST'].'/Home/Bindmail/checkmail?r='.$rand_str.'|'.$ac.'|'.$stu.'|'.$code;
        $content .= "<br> 复制以上链接在浏览器打开即可激活邮箱.";
//        $content = '点此链接: '.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/bindemail/'.$rand_str.'/'.$ac.'/'.$stu.'/'.$code.' 激活邮箱.';
        
        $send_res = mailsend($email,'邮箱激活 - 小莺出国',$content);// 发送邮件

        if (!$send_res['status']) $this->ajaxReturn(['result'=>false,'msg'=>$send_res['msg']]);// 邮件发送失败

        $save_res = M('Students')->where('id='.$stu)->save(['checkmail'=>$rand_str]);

        if (!$save_res) $this->ajaxReturn(['result'=>false,'msg'=>'网络异常,请联系管理员.']);// 邮件发送成功,但数据存储失败

        $this->ajaxReturn([ 'result'=>true, 'msg'=>'邮件发送成功,请至邮箱按照提示激活您的邮箱.' ]);// 发送成功

    }

    public function checkmail()
    {
        $par = explode('|',I('get.r')); // 处理参数

        // 获取参数 0=激活码 1=邮箱 2=用户编号 3=冗余参数
        $code = $par[0];
        $email = $par[1];
        $stu = $par[2];

        header('content-type:text/html;charset=utf-8');

        if (!$stu) $this->redirect('/','','2','非法操作: 403');// 缺少用户标识
        if (!$code) $this->redirect('/','','2','非法操作: 403');// 缺少激活码

        $info = M('Students')->field('id,code,realname,mobile,email,password,checkmail')->find($stu);// 获取用户信息
        if (!$info) $this->redirect('/','','2','非法操作: 404');// 用户不存在

        if ($info['checkmail'] != $code) $this->redirect('/','','2','非法操作: 无效的激活码.');// 激活码不正确

        $save_res = M('Students')->where('id='.$stu)->save(['email'=>$email]);// 更新邮箱

        if (!$save_res) $this->redirect('/','','2','网络异常,请稍后再试.');// 激活码不正确// 邮件发送成功,但数据存储失败

        // TODO 这里模拟用户登录
        session('_student', $info);

        $this->redirect('/user#/user','','2','激活成功...');// 激活成功

    }
}