<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/9/21
 * Time: 11:08
 */

namespace onlinemall\Controller;

use Home\Model\CrmProductModel;
use Think\Controller;

class IndexController extends Controller
{
    /**
     * 服务商城数据接口
     * 参数请求方式: post
     */
    public function getProduct()
    {
        // 实例化产品模型
        $product_model = new CrmProductModel();

        // 获取产品
        $product_list = $product_model->getProductByCate();

        // 返回数据
        $this->ajaxReturn($product_list);
    }
}