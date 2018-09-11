<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Model\LetterModel;
use Think\Exception;

class LetterController extends BaseController
{
    public function index()
    {
        $letterModel            =   new LetterModel();

        $result                 =   $letterModel->where(['student_id'=>['eq',session('_student.id')]])
            ->order('status asc,create_at desc')
            ->select();


        $this->ajaxReturn( [
            'result'            =>  true,
            'lists'             =>  $result,
            'desc'              =>  'status:信件状态 0.未读 1.已读 2.标记; from_type:来源类型 1.教师 2.学员 3.系统通知',
            '_sql'              =>  $letterModel->_sql(),
        ] );
    }

    public function read()
    {
        try{
            // TODO change
            if( IS_POST && IS_AJAX ){
                $letter_id          =   I('post.id', '', 'int');
                $letter_id || E('参数缺失');
                $letterModel        =   new LetterModel();
                $letterModel->where(['id'=>['eq',$letter_id], 'student_id'=>['eq',session('_student.id')]])->save(['status'=>1])===false
                &&  E($letterModel->getDbError());

                $this->ajaxReturn(['result'=>true]);
            }
            E('非法操作');
        }catch (Exception $e){
            $this->ajaxReturn([
                'result'    =>  false,
                'error'     =>  $e->getMessage()
            ]);
        }
    }
}