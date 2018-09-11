<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/9/10
 * Time: 13:40
 */

namespace Home\Model;

use Think\Model;

class DocCateModel extends Model
{
    protected $dbName = 'resources';

    protected $tablePrefix = '';

    protected $tableName = 'cate';

    public function getCates()
    {
        $cate = $this->where(['parent_id'=>['eq',0]])->select()?:[];

        if ($cate)
        {
            // 获取分类下的子分类
            foreach ($cate as $k => $v)
            {
                $cate[$k]['son'] = $this->where(['parent_id'=>['eq',$v['id']]])->select()?:[];
            }
        }

        return $cate;
    }

}