<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/9/10
 * Time: 13:15
 */

namespace Home\Model;

use Think\Model;

class DocModel extends Model
{
    protected $dbName = 'resources';

    protected $tablePrefix = '';

    protected $tableName = 'mate';

    public function getMates()
    {
        $condition = I('post.');

        // 分页
        $limit_num = $condition['limit_num']?:10;// 每页显示条数
        $start = $condition['page']?(($condition['page']-1)*$limit_num):0;// 查询起始值

        // 如果按分类查询
        if ($condition['category'])
        {
            $cate_model = new DocCateModel();
            // 先判断分类是否有子分类
            $son_cates = $cate_model->where(['parent_id'=>['eq',$condition['category']]])->select()?:[];
            
            // 如果有子分类
            if ($son_cates)
            {
                $cate_ids = array_map(function ($v){
                    return $v['id'];
                },$son_cates);
                
                // 如果子分类ids不为空 算是多余的判断...
                if ($cate_ids)
                {
                    array_unshift($cate_ids,$condition['category']);// 把父分类插入到分类ids中 (父分类也是可选可关联的分类)

                    // 获取符合条件的分类下的资料IDS
                    $doc_cate_rel_model = new DocCateRelModel();
                    $doc_ids = $doc_cate_rel_model->where(['cate_id'=>['in',$cate_ids]])->select()?:[];

                    // 如果资料id为空 没有符合条件的资料
                    if (!$doc_ids) return [];

                    // 转化资料ids为一维数组
                    $docids = array_map(function ($v){
                        return $v['mate_id'];
                    },$doc_ids);

                    // 如果转换后的资料id为空 没有符合条件的资料
                    if (!$docids) return [];

                    // 设置查询条件
                    $where['id'] = ['in',$docids];
                }
            }else{
                // 如果没有子分类,获取当前分类下的资料IDS
                $doc_cate_rel_model = new DocCateRelModel();
                $doc_ids = $doc_cate_rel_model->where(['cate_id'=>['eq',$condition['category']]])->select()?:[];
                
                // 如果资料id为空 没有符合条件的资料
                if (!$doc_ids) return [];

                // 转化资料ids为一维数组
                $docids = array_map(function ($v){
                    return $v['mate_id'];
                },$doc_ids);

                // 如果转换后的资料id为空 没有符合条件的资料
                if (!$docids) return [];

                // 设置查询条件
                $where['id'] = ['in',$docids];
            }
        }

        // 查询资料数据
        if ($where)
        {
            $count = $this->where($where)->limit($start,$limit_num)->count();
            $mates = $this->where($where)->limit($start,$limit_num)->select()?:[];
        }else{
            $count = $this->limit($start,$limit_num)->count();
            $mates = $this->limit($start,$limit_num)->select()?:[];
        }

        $page_count = ceil($count / $limit_num);// 总页数

        // 如果资料为空
        if (!$mates) return [];

        // 如果资料数据为空,获取资料下的文档
        foreach ($mates as $mate_k => $mate_v)
        {
            $doc_files_model = new DocFileModel();
            $mates[$mate_k]['files'] = $doc_files_model->where(['ral_id'=>['eq',$mate_v['id']]])->select()?:[];// todo
        }

        return [
            'count' => $count,// 总条数
            'page_count' => $page_count,// 总页数
            'mates' => $mates,// 资料列表
        ];
    }
}