<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Model\StudentModel;
use Think\Controller;
use Think\Controller\RpcController;

class IndexController extends Controller
{
    public function index()
    {
        $this->display();
    }

    public function test ()
    {
        exit('-...');
        $model      =   new StudentModel();
        $info       =   $model->find(1);
        if( session('?_student') )
            return ;


        session('_student', $info);

        dump( $info );

    }

    public function isLogin ()
    {
        $is_login               =   session('?_student');
        $result                 =   [
            'result'    =>  $is_login,
            'code'      =>  $is_login ? 200 : 403,
        ];

        $this->ajaxReturn( $result );
    }

}