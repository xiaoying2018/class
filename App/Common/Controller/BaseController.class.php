<?php

namespace Common\Controller;

use Think\Controller;
use Think\Exception;
use Think\Upload;

class BaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        IS_JSONP && $this->_origin();
        $this->checkAuth();
    }

    protected function checkAuth ()
    {
        try{
            // 登陆验证
             $this->_loginCheck() || E('请先登录', 203);
        }catch (Exception $e){
            $result         =   [
                'result'        =>  false,
                'code'          =>  403,
                'msg'           =>  $e->getMessage(),
            ];
            $e->getCode()==203 && ($result['url']=U('/'));

            $this->response($result);
        }
    }

    protected function _loginCheck()
    {
        return session('?_student');
    }

    protected function _origin ()
    {
        // 请求来源
        $http_origin                =   false;
        if( array_key_exists( 'HTTP_ORIGIN', $_SERVER ) && $_SERVER['HTTP_ORIGIN'] ){
            $http_origin        =   $_SERVER['HTTP_ORIGIN'];
        }elseif( array_key_exists( 'HTTP_REFERER', $_SERVER ) && $_SERVER['HTTP_REFERER'] ){
            $http_origin        =   $_SERVER['HTTP_REFERER'];
        }
        // 来源判断
        if( $http_origin === false )    return ;
        // 获取来源主域
        preg_match("/^(?:http|https):\/\/([^\/]+)/", $http_origin, $matches);

        if( !array_key_exists( 1, $matches ) )
            return ;

        header('Access-Control-Allow-Origin: '.$matches[0]);
        header("Access-Control-Allow-Methods: POST, GET");
        header('Access-Control-Allow-Headers:x-requested-with');
        header("Access-Control-Allow-Credentials: true");
        return;
    }

    protected function uploadOne($fileKey,$path='')
    {
        $upload                 =   new Upload();
        $upload->maxSize        =   3145728 ;// 设置附件上传大小
        $upload->exts           =   array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath       =   './Upload/'; // 设置附件上传根目录
        $upload->savePath       =   $path; // 设置附件上传（子）目录
        $upload->autoSub        =   true;
        $upload->subName        =   ['date','Y-m-d'];
        if( $info=$upload->uploadOne($_FILES[$fileKey]) ){
            return $info;
        }
        $this->error            =   $upload->getError();
        return false;
    }

}