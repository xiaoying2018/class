<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/8/27
 * Time: 15:58
 */

namespace Home\Model;

use Apply\Model\CRMBaseModel;

class CourseBespeakModel extends CRMBaseModel
{
    protected $tableName                    =   'product';

    public function getCourseProduct()
    {
        // 获取类别为留学培训的产品
        $course_list = $this->where(['category_id'=>['eq',1],['on_sale'=>['eq','是']]])->select();
        // 通过产品获取当前产品下有哪些课程
        foreach ($course_list as $k => $v)
        {
            $has_courses = M('CourseProduct')->where(['product_id'=>['eq',$v['product_id']]])->select();
            $has_courses_ids = array_map(function ($v){
                return $v['course_id'];
            },$has_courses);
            $course_list[$k]['has_courses'] = M('Course')->where(['id'=>['in',$has_courses_ids]])->select();
            // 以上 course_list 为左侧导航栏

            // 预约课列表
            foreach ($course_list[$k]['has_courses'] as $course_key => $course_val) {
                $course_section[$course_key] = M('CourseSection')->where(['course_id'=>['eq',$course_val['id']]])->select();// 获取当前课程下所有的课时
                foreach ($course_section[$course_key] as $section_key => $section_val)
                {
                    // 获取当前课时所有的排课信息
                    $course_section[$course_key]['all_schedule'] = M('CourseSchedule')->where(['section_id'=>['eq',$section_val['id']]])->select();
                }
            }

        }

        return $course_section;

    }

    public function getCourseAndPrice()
    {
        // 获取类别为留学培训的产品
        $course_list = $this->where(['category_id'=>['eq',1],'on_sale'=>['eq','是']])->select();

        $courses_res['free'] = [];// 免费课程
        $courses_res['paid'] = [];// 付费课程

        // 通过产品获取当前产品下有哪些课程
        foreach ($course_list as $k => $v)
        {

            // 如果当前产品价格超过两万,则忽略当前产品下所有课程 TODO 这里1000000上线后改为100000
            if (intval($v['suggested_price']) > 1000000) continue;

            $has_courses = M('CourseProduct')->where(['product_id'=>['eq',$v['product_id']]])->select();
            $has_courses_ids = array_map(function ($v){
                return $v['course_id'];
            },$has_courses);
            
            if ($has_courses_ids)
            {
                foreach ($has_courses_ids as $id_k =>$id_v)
                {
                    $_course_data = M('Course')->find($id_v)?:[];
                    $_course_data['price'] = $v['suggested_price']?:0;

                    if ($_course_data['price'] == 0)
                    {
                        $courses_res['free'][] = $_course_data?:[];
                    }else{
                        $courses_res['paid'][] = $_course_data?:[];
                    }

                }
            }

        }

        return $courses_res;

    }

}