<?php
namespace Home\Model;

use Common\Model\EModel;

class LetterModel extends EModel
{
    protected $tableName                    =   'letter';

    public function unreadCount ($where=null)
    {
        $_where['status']           =  ['eq',0];
        if( !is_null($where) && is_array($where) )
            $_where         =   array_merge( $_where, $where );
        return $this->where($_where)->count();
    }

    public function getNewestLetter($where=null)
    {
        $_where           =  [];
        if( !is_null($where) && is_array($where) )
            $_where         =   array_merge( $_where, $where );
        $data=$this->where($_where)->order('id desc')->find();
        if($data['from_type']==1){
            $data['from_user']=M('students')->where('id='.$data['from_user_id'])->realname;
        }elseif ($data['from_type']==2){
            //$data['from_user']=M('teacher_detail')->where('user_id='.$data['from_user_id'])->realname;
            $sql='select `full_name` from mxcrm.mx_user where user_id='.$data['from_user_id'];
            $data1=M()->query($sql);
            $data['from_user']=$data1[0]['full_name'];
        }else{
            $data['from_user']='系统通知';
        }
        return $data;
    }
}