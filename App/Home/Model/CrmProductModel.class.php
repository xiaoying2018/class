<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/9/21
 * Time: 11:13
 */

namespace Home\Model;

use Apply\Model\CRMBaseModel;

class CrmProductModel extends CRMBaseModel
{
    protected $dbName = 'mxcrm';

    protected $trueTableName = 'mx_product';

    /**
     * @param int $cate 产品类别 1=留学培训 2=留学申请 3=附加服务
     */
    public function getProductByCate($cate)
    {
        $par = I('post.');

        // 默认过滤已下架/隐藏/删除的产品 todo 这里使用 $where['on_sale'] = ['eq','是'] 无效? 9-21 dragon
        $where = "`on_sale` = '是' AND `is_deleted` = '0'";

        // 如果按分类搜索
        if ($par['cate']) $where .= " AND `category_id` = {$par['cate']}";

        // 如果按名称搜索
        if ($par['name']) $where .= " AND `name` like '%{$par['name']}%'";
        
        // 分页条件
        $current_page = $par['page']?:1;// 当前页
        $limit = $par['page_size']?:10;// 每页显示条数
        $start = ($current_page-1) * $limit;// 查询起始条件


        try{
            // 需要在商城展示的字段
            $target_field = 'product_id,category_id,name,code,teacher,suggested_price,standard,description,product_num,sketch,crm_kwdczn,crm_fgempq,product_info';
            $list = $this->field($target_field)->where($where)->limit($start,$limit)->select();
            $count = $this->where($where)->count();
        }catch (\Exception $exception){
            return ['status'=>false,'msg'=>$exception->getMessage()];
        }

        return ['status'=>true,'count'=>$count,'data'=>$list];
    }
}