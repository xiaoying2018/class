<?php
namespace Home\Model;

use Common\Model\EModel;

class CourseModel extends EModel
{
    protected $tableName                =   'course';

    public function getAllBanji()
    {
        $field = 'id,name,category,detail,member_type,create_at,pic,is_show,is_open,is_qianyue,people_num';

        $order = 'create_at DESC';

        $where = ['status'=>1];

        $res = $this->field($field)->where($where)->order($order)->select()?:[];

        return $res;
    }

}