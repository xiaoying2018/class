<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/9/7
 * Time: 9:15
 */

namespace Mall\Model;

use Common\Model\EModel;
use Home\Model\CourseModel;
use Home\Model\PeriodModel;
use Home\Model\ScheduleModel;
use Home\Model\SectionModel;
use Home\Model\TeacherModel;

class SectionCateModel extends EModel
{
    protected $tableName = 'course_section_cate';

    public function searchBanjiByName($search_name)
    {
        $condition = I('post.');
        // 分页
        $limit_num = $condition['limit_num']?:10;// 每页显示条数
        $start = $condition['page']?(($condition['page']-1)*$limit_num):0;// 查询起始值

        // 获取首页展示的所有班
        $count = M('Course')->where(['status'=>['eq',1],'name'=>['like','%'.$search_name.'%']])->count();
        // 如果需要分页
//        $all_banji = M('Course')->where(['status'=>['eq',1],'name'=>['like','%'.$search_name.'%']])->limit($start,$limit_num)->select();
        $all_banji = M('Course')->where(['status'=>['eq',1],'name'=>['like','%'.$search_name.'%']])->select();

        // 总页数
        $page_count = ceil($count / $limit_num);

        // 获取班级下的课程数和课时数
        if ($all_banji)
        {
            foreach ($all_banji as $banji_k => $banji_v)
            {
                // 预留为0 便于前端识别
                $all_banji[$banji_k]['start_time'] = 0;
                $all_banji[$banji_k]['teacher_name'] = 0;
                // 获取班级下的课程
                $all_banji[$banji_k]['show_course'] = [];
                // 班级下的课程ids
                $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();
                if ($_course_ids)
                {
                    // 二维转一维
                    $course_ids = array_map(function ($v){
                        return $v['section_cate_id'];
                    },$_course_ids);

                    // 班级下的课程数
                    $all_banji[$banji_k]['course_num'] = count($course_ids);
                    // 班级下所有课程的总课时数
                    if ($course_ids)
                    {
                        // 获取班级下的课程
                        $show_course = $this->where(['id' => ['in', $course_ids],'status'=>['eq',1]])->limit(8)->select();
                        // 获取课程下的课时数
                        if ($show_course) {
                            foreach ($show_course as $k => $v) {
                                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
                            }
                        }
                        $all_banji[$banji_k]['show_course'] = $show_course;
                        $all_banji[$banji_k]['section_num'] = M('course_section')->where(['course_id'=>['in',$course_ids]])->count();
                    }else{
                        $all_banji[$banji_k]['section_num'] = 0;
                    }
                }

                // 班级下的班期信息 和 老师信息
                $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();// 班期信息
                if ($banji_banci)
                {
                    // 班主任ids
                    $banzhuren_ids = array_map(function ($v){
                        return $v['headmaster_id'];
                    },$banji_banci);
                    // 班次 ids
                    $banci_ids = array_map(function ($v){
                        return $v['id'];
                    },$banji_banci);
                    if ($banci_ids)
                    {
                        $new_paike = (new ScheduleModel())->where(['period_id'=>['in',$banci_ids]])->order('start_time')->find()?:[];// 班次内最早的排课
                        $all_banji[$banji_k]['start_time'] = $new_paike['start_time']?:0;// 班次开始时间
                        $_teacher_id = $new_paike['teacher_id']?:0;// 班次开始时间
                        if ($_teacher_id)
                        {
                            // 老师名字
                            $all_banji[$banji_k]['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$_teacher_id]])->find()['full_name']?:0;
                        }
                    }
                }

            }
        }


        return [
            'count' => $count,// 总条数
            'page_count' => $page_count,// 总页数
            'banji' => $all_banji,// 课程列表
        ];
    }

    public function searchCourseByName($search_name)
    {
        $condition = I('post.');
        // 分页
        $limit_num = $condition['limit_num']?:2;// 每页显示条数
        $start = $condition['page']?(($condition['page']-1)*$limit_num):0;// 查询起始值

        // 获取公开课课程
        $count = $this->where(['status'=>['eq',1],'name'=>['like','%'.$search_name.'%']])->count();
        // 如果需要分页
//        $show_course = $this->where(['status'=>['eq',1],'name'=>['like','%'.$search_name.'%']])->limit($start,$limit_num)->select();
        $show_course = $this->where(['status'=>['eq',1],'name'=>['like','%'.$search_name.'%']])->select();

        // 总页数
        $page_count = ceil($count / $limit_num);

        // 获取课程下的课时数
        if ($show_course) {
            foreach ($show_course as $k => $v) {
                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
            }
            return [
                'count' => $count,// 总条数
                'page_count' => $page_count,// 总页数
                'courses' => $show_course,// 课程列表
            ];
        }

        return [];
    }

    // 班级/课程详情页 获取推荐班级,每次获取两条(随机)
    public function getTuijianBanji()
    {
        // 获取首页展示的所有班
        $all_banji = M('Course')->where(['is_show'=>['eq',1],'status'=>['eq',1]])->order('people_num desc')->limit(10)->select();

        // 获取班级下的课程数和课时数
        if ($all_banji)
        {
            foreach ($all_banji as $banji_k => $banji_v)
            {
                // 预留为0 便于前端识别
                $all_banji[$banji_k]['start_time'] = 0;
                $all_banji[$banji_k]['teacher_name'] = 0;
                // 获取班级下的课程
                $all_banji[$banji_k]['show_course'] = [];
                // 班级下的课程ids
                $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();
                if ($_course_ids)
                {
                    // 二维转一维
                    $course_ids = array_map(function ($v){
                        return $v['section_cate_id'];
                    },$_course_ids);

                    // 班级下的课程数
                    $all_banji[$banji_k]['course_num'] = count($course_ids);
                    // 班级下所有课程的总课时数
                    if ($course_ids)
                    {
                        // 获取班级下的课程
                        $show_course = $this->where(['id' => ['in', $course_ids],'status'=>['eq',1]])->limit(8)->select();
                        // 获取课程下的课时数
                        if ($show_course) {
                            foreach ($show_course as $k => $v) {
                                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
                            }
                        }
                        $all_banji[$banji_k]['show_course'] = $show_course;
                        $all_banji[$banji_k]['section_num'] = M('course_section')->where(['course_id'=>['in',$course_ids]])->count();
                    }else{
                        $all_banji[$banji_k]['section_num'] = 0;
                    }
                }

                // 班级下的班期信息 和 老师信息
                $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();// 班期信息
                if ($banji_banci)
                {
                    // 班主任ids
                    $banzhuren_ids = array_map(function ($v){
                        return $v['headmaster_id'];
                    },$banji_banci);
                    // 班次 ids
                    $banci_ids = array_map(function ($v){
                        return $v['id'];
                    },$banji_banci);
                    if ($banci_ids)
                    {
                        $new_paike = (new ScheduleModel())->where(['period_id'=>['in',$banci_ids]])->order('start_time')->find()?:[];// 班次内最早的排课
                        $all_banji[$banji_k]['start_time'] = $new_paike['start_time']?:0;// 班次开始时间
                        $_teacher_id = $new_paike['teacher_id']?:0;// 班次开始时间
                        if ($_teacher_id)
                        {
                            // 老师名字
                            $all_banji[$banji_k]['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$_teacher_id]])->find()['full_name']?:0;
                        }
                    }
                }

            }
        }

        return $all_banji;
        
    }

    public function getShowCourses()
    {

//            category 对照表
//            ['id'=>1,'name'=>'修士考'],
//            ['id'=>2,'name'=>'留考校内考'],
//            ['id'=>3,'name'=>'日语'],
//            ['id'=>4,'name'=>'艺术'],

        $banji['xiushi'] = [];// 修士考
        $banji['liukao'] = [];// 留考校内考
        $banji['baolu'] = [];// 签约保录
        $banji['gongkai'] = [];// 热门公开

        // 获取首页展示的所有班
        $all_banji = M('Course')->where(['is_show'=>['eq',1],'status'=>['eq',1]])->order('create_at desc')->limit(8)->select();
        // 获取班级下的课程数和课时数
        if ($all_banji)
        {
            foreach ($all_banji as $banji_k => $banji_v)
            {
                // 预留为0 便于前端识别
                $all_banji[$banji_k]['start_time'] = 0;
                $all_banji[$banji_k]['teacher_name'] = 0;
                // 获取班级下的课程
                $all_banji[$banji_k]['show_course'] = [];
                // 班级下的课程ids
                $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();
                if ($_course_ids)
                {
                    // 二维转一维
                    $course_ids = array_map(function ($v){
                        return $v['section_cate_id'];
                    },$_course_ids);

                    // 班级下的课程数
                    $all_banji[$banji_k]['course_num'] = count($course_ids);
                    // 班级下所有课程的总课时数
                    if ($course_ids)
                    {
                        // 获取班级下的课程
                        $show_course = $this->where(['id' => ['in', $course_ids],'status'=>['eq',1]])->limit(8)->select();
                        // 获取课程下的课时数
                        if ($show_course) {
                            foreach ($show_course as $k => $v) {
                                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
                            }
                        }
                        $all_banji[$banji_k]['show_course'] = $show_course;
                        $all_banji[$banji_k]['section_num'] = M('course_section')->where(['course_id'=>['in',$course_ids]])->count();
                    }else{
                        $all_banji[$banji_k]['section_num'] = 0;
                    }
                }

                // todo 班级下的班期信息 和 老师信息
                $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();// 班期信息
                if ($banji_banci)
                {
                    // 班主任ids
                    $banzhuren_ids = array_map(function ($v){
                        return $v['headmaster_id'];
                    },$banji_banci);
                    // 班次 ids
                    $banci_ids = array_map(function ($v){
                        return $v['id'];
                    },$banji_banci);
                    if ($banci_ids)
                    {
                        $new_paike = (new ScheduleModel())->where(['period_id'=>['in',$banci_ids]])->order('start_time')->find()?:[];// 班次内最早的排课
                        $all_banji[$banji_k]['start_time'] = $new_paike['start_time']?:0;// 班次开始时间
                        $_teacher_id = $new_paike['teacher_id']?:0;// 班次开始时间
                        if ($_teacher_id)
                        {
                            // 老师名字
                            $all_banji[$banji_k]['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$_teacher_id]])->find()['full_name']?:0;
                        }
//                    $all_banji[$banji_k]['banzhuren_id'] = $banci_ids;// 班主任ids
                    }
                }

                // 新的班级分类
                if ($banji_v['category'] == 1)
                {
                    $banji['xiushi']['by_new'][] = $all_banji[$banji_k];// 修士考试
                }

                if ($banji_v['category'] == 2)
                {
                    $banji['liukao']['by_new'][] = $all_banji[$banji_k];// 留考校内考
                }

//                if ($banji_v['is_qianyue'] == 1)
//                {
                    $banji['baolu']['by_new'][] = $all_banji[$banji_k];// 签约保录
//                }

                if ($banji_v['is_open'] == 1)
                {
                    $banji['gongkai']['by_new'][] = $all_banji[$banji_k];// 热门公开
                }

//                // 班级分类
//                switch ($banji_v['category'])
//                {
//                    case 1:
//                        $banji[1][] = $all_banji[$banji_k];// 修士考试班
//                        break;
//                    case 2:
//                        $banji[2][] = $all_banji[$banji_k];// 留考校内考班
//                        break;
//                    case 3:
//                        $banji[3][] = $all_banji[$banji_k];// 热门公开课班
//                        break;
//                    case 4:
//                        $banji[4][] = $all_banji[$banji_k];// 签约保录班
//                        break;
//                    default:
//                        $banji[5][] = $all_banji[$banji_k];// 其他班级
//                }
            }
        }

        // 获取首页展示的所有班
        $all_banji = M('Course')->where(['is_show'=>['eq',1],'status'=>['eq',1]])->order('people_num desc')->limit(8)->select();
        // 获取班级下的课程数和课时数
        if ($all_banji)
        {
            foreach ($all_banji as $banji_k => $banji_v)
            {
                // 预留为0 便于前端识别
                $all_banji[$banji_k]['start_time'] = 0;
                $all_banji[$banji_k]['teacher_name'] = 0;
                // 获取班级下的课程
                $all_banji[$banji_k]['show_course'] = [];
                // 班级下的课程ids
                $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();
                if ($_course_ids)
                {
                    // 二维转一维
                    $course_ids = array_map(function ($v){
                        return $v['section_cate_id'];
                    },$_course_ids);

                    // 班级下的课程数
                    $all_banji[$banji_k]['course_num'] = count($course_ids);
                    // 班级下所有课程的总课时数
                    if ($course_ids)
                    {
                        // 获取班级下的课程
                        $show_course = $this->where(['id' => ['in', $course_ids],'status'=>['eq',1]])->limit(8)->select();
                        // 获取课程下的课时数
                        if ($show_course) {
                            foreach ($show_course as $k => $v) {
                                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
                            }
                        }
                        $all_banji[$banji_k]['show_course'] = $show_course;
                        $all_banji[$banji_k]['section_num'] = M('course_section')->where(['course_id'=>['in',$course_ids]])->count();
                    }else{
                        $all_banji[$banji_k]['section_num'] = 0;
                    }
                }

                // todo 班级下的班期信息 和 老师信息
                $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();// 班期信息
                if ($banji_banci)
                {
                    // 班主任ids
                    $banzhuren_ids = array_map(function ($v){
                        return $v['headmaster_id'];
                    },$banji_banci);
                    // 班次 ids
                    $banci_ids = array_map(function ($v){
                        return $v['id'];
                    },$banji_banci);
                    if ($banci_ids)
                    {
                        $new_paike = (new ScheduleModel())->where(['period_id'=>['in',$banci_ids]])->order('start_time')->find()?:[];// 班次内最早的排课
                        $all_banji[$banji_k]['start_time'] = $new_paike['start_time']?:0;// 班次开始时间
                        $_teacher_id = $new_paike['teacher_id']?:0;// 班次开始时间
                        if ($_teacher_id)
                        {
                            // 老师名字
                            $all_banji[$banji_k]['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$_teacher_id]])->find()['full_name']?:0;
                        }
//                    $all_banji[$banji_k]['banzhuren_id'] = $banci_ids;// 班主任ids
                    }
                }

                // 新的班级分类
                if ($banji_v['category'] == 1)
                {
                    $banji['xiushi']['by_people'][] = $all_banji[$banji_k];// 修士考试
                }

                if ($banji_v['category'] == 2)
                {
                    $banji['liukao']['by_people'][] = $all_banji[$banji_k];// 留考校内考
                }

//                if ($banji_v['is_qianyue'] == 1)
//                {
                $banji['baolu']['by_people'][] = $all_banji[$banji_k];// 签约保录
//                }

                if ($banji_v['is_open'] == 1)
                {
                    $banji['gongkai']['by_people'][] = $all_banji[$banji_k];// 热门公开
                }

//                // 班级分类
//                switch ($banji_v['category'])
//                {
//                    case 1:
//                        $banji[1][] = $all_banji[$banji_k];// 修士考试班
//                        break;
//                    case 2:
//                        $banji[2][] = $all_banji[$banji_k];// 留考校内考班
//                        break;
//                    case 3:
//                        $banji[3][] = $all_banji[$banji_k];// 热门公开课班
//                        break;
//                    case 4:
//                        $banji[4][] = $all_banji[$banji_k];// 签约保录班
//                        break;
//                    default:
//                        $banji[5][] = $all_banji[$banji_k];// 其他班级
//                }
            }
        }

        return [
            'banji' => $banji,  // 班级列表
        ];
    }

    // 获取班级详情
    public function getBanjiInfo($id)
    {
        // 获取指定ID的班级
        $banji = M('Course')->where(['status'=>['eq',1],'id'=>['eq',$id]])->find();

        // 如果没有符合条件的班级
        if (!$banji) return [];

        // 获取班级下的课程数和课时数
        $banji['start_time'] = 0;// 班级最近一期开始时间
        $banji['teacher_name'] = 0;// 班级最近一期授课老师
        $banji['show_course'] = 0;// 班级包含的课程
        // 班级下的课程ids
        $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id'=>['eq',$banji['id']]])->select();
        
        if ($_course_ids)
        {
            // 二维转一维
            $course_ids = array_map(function ($v){
                return $v['section_cate_id'];
            },$_course_ids);

            // 班级下的课程数
            $banji['course_num'] = count($course_ids);
            // 班级下所有课程的总课时数
            if ($course_ids)
            {
                // 获取班级下的课程
                $show_course = $this->where(['id' => ['in', $course_ids],'status'=>['eq',1]])->select();
                // 班级下的课时信息
                $banji['sections'] = [];
                // 获取课程下的课时数
                if ($show_course) {
                    foreach ($show_course as $k => $v) {
                        $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
                        $_tmp_sections = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->select();
                        if ($_tmp_sections) $banji['sections'] = array_merge($banji['sections'],$_tmp_sections);
                    }
                }
                $banji['show_course'] = $show_course;
                $banji['section_num'] = M('course_section')->where(['course_id'=>['in',$course_ids]])->count();
            }else{
                $banji['section_num'] = 0;
            }
        }

        // 班级下的班期信息 和 老师信息
        $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id'=>['eq',$banji['id']]])->select();// 班期信息
        if ($banji_banci)
        {
            // 班主任ids
//            $banzhuren_ids = array_map(function ($v){
//                return $v['headmaster_id'];
//            },$banji_banci);
            // 班次 ids
            $banci_ids = array_map(function ($v){
                return $v['id'];
            },$banji_banci);
            if ($banci_ids)
            {
                $new_paike = (new ScheduleModel())->where(['period_id'=>['in',$banci_ids]])->order('start_time')->find()?:[];// 班次内最早的排课
                $banji['start_time'] = $new_paike['start_time']?:0;// 班次开始时间
                $_teacher_id = $new_paike['teacher_id']?:0;// 班次开始时间
                if ($_teacher_id)
                {
                    // 老师名字
                    $banji['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$_teacher_id]])->find()['full_name']?:0;
                }
            }
        }

        return $banji;
    }

    // 获取课程详情
    public function getCourseInfo($id)
    {
        // 获取指定ID的课程
        $course = $this->where(['status'=>['eq',1],'id'=>['eq',$id]])->find();

        // 如果没有符合条件的课程
        if (!$course) return [];

        // 获取课程下的课时数
        $course['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $course['id']]])->count();
        $course['sections'] = (new SectionModel())->where(['course_id' => ['eq', $course['id']]])->select();

        return $course;
        
    }

    public function getBaoluBan()
    {

        $par = I('post.');

        $order_by = 'create_at desc';// 默认时间倒序

        // 如果按报名人数排序
        if ($par['sort_by_people_num'])
        {
            $order_by = 'people_num desc';
        }

        $where['status'] = ['eq',1];

        if ($par['category'])
        {
            $where['category'] = ['eq',$par['category']];
        }

        // 分页
        $limit_num = $par['limit_num']?:10;// 每页显示条数
        $start = $par['page']?(($par['page']-1)*$limit_num):0;// 查询起始值

        // 符合条件的班级总数
        $all_banji_count = M('Course')->where($where)->order($order_by)->count();
        // 总页数
        $all_banji_page_count = ceil($all_banji_count/$limit_num);

        // 获取所有班 所有班级都是保录班
        $all_banji = M('Course')->where($where)->order($order_by)->limit($start,$limit_num)->select();
        // 获取班级下的课程数和课时数
        if ($all_banji)
        {
            foreach ($all_banji as $banji_k => $banji_v)
            {
                // 预留为0 便于前端识别
                $all_banji[$banji_k]['start_time'] = 0;
                $all_banji[$banji_k]['teacher_name'] = 0;
                // 获取班级下的课程
                $all_banji[$banji_k]['show_course'] = [];
                // 班级下的课程ids
                $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();
                if ($_course_ids)
                {
                    // 二维转一维
                    $course_ids = array_map(function ($v){
                        return $v['section_cate_id'];
                    },$_course_ids);

                    // 班级下的课程数
                    $all_banji[$banji_k]['course_num'] = count($course_ids);
                    // 班级下所有课程的总课时数
                    if ($course_ids)
                    {
                        // 获取班级下的课程
                        $show_course = $this->where(['id' => ['in', $course_ids],'status'=>['eq',1]])->select();
                        // 获取课程下的课时数
                        if ($show_course) {
                            foreach ($show_course as $k => $v) {
                                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
                            }
                        }
                        $all_banji[$banji_k]['show_course'] = $show_course;
                        $all_banji[$banji_k]['section_num'] = M('course_section')->where(['course_id'=>['in',$course_ids]])->count();
                    }else{
                        $all_banji[$banji_k]['section_num'] = 0;
                    }
                }

                // todo 班级下的班期信息 和 老师信息
                $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();// 班期信息
                if ($banji_banci)
                {
                    // 班主任ids
                    $banzhuren_ids = array_map(function ($v){
                        return $v['headmaster_id'];
                    },$banji_banci);
                    // 班次 ids
                    $banci_ids = array_map(function ($v){
                        return $v['id'];
                    },$banji_banci);
                    if ($banci_ids)
                    {
                        $new_paike = (new ScheduleModel())->where(['period_id'=>['in',$banci_ids]])->order('start_time')->find()?:[];// 班次内最早的排课
                        $all_banji[$banji_k]['start_time'] = $new_paike['start_time']?:0;// 班次开始时间
                        $_teacher_id = $new_paike['teacher_id']?:0;// 班次开始时间
                        if ($_teacher_id)
                        {
                            // 老师名字
                            $all_banji[$banji_k]['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$_teacher_id]])->find()['full_name']?:0;
                        }
//                    $all_banji[$banji_k]['banzhuren_id'] = $banci_ids;// 班主任ids
                    }
                }

            }
        }

        return [
            'count' => $all_banji_count,// 总条数
            'page_count' => $all_banji_page_count,// 总页数
            'banji' => $all_banji,  // 班级列表
        ];
    }

    public function getOpenCourse()
    {

        $par = I('post.');

        $order_by = 'create_time desc';// 默认时间倒序
        
        // 如果按报名人数排序
        if ($par['sort_by_people_num'])
        {
            $order_by = 'people_num desc';
        }

        // 分页
        $limit_num = $par['limit_num']?:10;// 每页显示条数
        $start = $par['page']?(($par['page']-1)*$limit_num):0;// 查询起始值

        // 符合条件的课程总数
        $all_count = $this->where(['is_open'=>['eq', 1],'status'=>['eq',1]])->count();
        // 总页数
        $all_page_count = ceil($all_count/$limit_num);

        // 获取公开课课程
        $show_course = $this->where(['is_open'=>['eq', 1],'status'=>['eq',1]])->order($order_by)->limit($start,$limit_num)->select();
        
        // 获取课程下的课时数
        if ($show_course) {
            foreach ($show_course as $k => $v) {
                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
            }
        }

        return [
            'count' => $all_count,// 总条数
            'page_count' => $all_page_count,// 总页数
            'open_course' => $show_course,
        ];
    }

    public function getXiaoneiCourse()
    {
        $par = I('post.');

        $order_by = 'create_time desc';// 默认时间倒序

        // 如果按报名人数排序
        if ($par['sort_by_people_num'])
        {
            $order_by = 'people_num desc';
        }

        // 分页
        $limit_num = $par['limit_num']?:10;// 每页显示条数
        $start = $par['page']?(($par['page']-1)*$limit_num):0;// 查询起始值

        // 符合条件的课程总数
        $all_count = $this->where(['neirong'=>['eq', 3],'status'=>['eq',1]])->count();
        // 总页数
        $all_page_count = ceil($all_count/$limit_num);

        // 获取公开课课程
        $show_course = $this->where(['neirong'=>['eq', 3],'status'=>['eq',1]])->order($order_by)->limit($start,$limit_num)->select();
        // 获取课程下的课时数
        if ($show_course) {
            foreach ($show_course as $k => $v) {
                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
            }
        }

        return [
            'count' => $all_count,// 总条数
            'page_count' => $all_page_count,// 总页数
            'open_course' => $show_course,
        ];
    }

    public function getZhiboCourse()
    {

        $par = I('post.');

        $order_by = 'create_time desc';// 默认时间倒序

        // 如果按报名人数排序
        if ($par['sort_by_people_num'])
        {
            $order_by = 'people_num desc';
        }

        // 分页
        $limit_num = $par['limit_num']?:10;// 每页显示条数
        $start = $par['page']?(($par['page']-1)*$limit_num):0;// 查询起始值

        // 符合条件的课程总数
        $all_count = $this->where(['zhibo'=>['eq', 1],'status'=>['eq',1]])->count();
        // 总页数
        $all_page_count = ceil($all_count/$limit_num);

        // 获取公开课课程
        $show_course = $this->where(['zhibo'=>['eq', 1],'status'=>['eq',1]])->order($order_by)->limit($start,$limit_num)->select();
        // 获取课程下的课时数
        if ($show_course) {
            foreach ($show_course as $k => $v) {
                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
            }
        }

        return [
            'count' => $all_count,// 总条数
            'page_count' => $all_page_count,// 总页数
            'open_course' => $show_course,
        ];
    }

    // 暂放,未用到
    public function getShowCourses_dierci()
    {

//        ['id'=>1,'name'=>'修士考试'],
//            ['id'=>2,'name'=>'留考校内考'],
//            ['id'=>3,'name'=>'热门公开课'],
//            ['id'=>4,'name'=>'保录班'],

        $banji[1] = [];// 修士考试班
        $banji[2] = [];// 留考校内考班
        $banji[3] = [];// 热门公开课班
        $banji[4] = [];// 签约保录班

        // 获取首页展示的所有班
        $all_banji = M('Course')->where(['is_show'=>['eq',1]])->select();
        // 获取班级下的课程数和课时数
        if ($all_banji)
        {
            foreach ($all_banji as $banji_k => $banji_v)
            {
                // 预留为0 便于前端识别
                $all_banji[$banji_k]['start_time'] = 0;
                $all_banji[$banji_k]['teacher_name'] = 0;
                // 获取班级下的课程
                $all_banji[$banji_k]['show_course'] = [];
                // 班级下的课程ids
                $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();
                if ($_course_ids)
                {
                    // 二维转一维
                    $course_ids = array_map(function ($v){
                        return $v['section_cate_id'];
                    },$_course_ids);

                    // 班级下的课程数
                    $all_banji[$banji_k]['course_num'] = count($course_ids);
                    // 班级下所有课程的总课时数
                    if ($course_ids)
                    {
                        // 获取班级下的课程
                        $show_course = $this->where(['id' => ['in', $course_ids]])->select();
                        // 获取课程下的课时数
                        if ($show_course) {
                            foreach ($show_course as $k => $v) {
                                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
                            }
                        }
                        $all_banji[$banji_k]['show_course'] = $show_course;
                        $all_banji[$banji_k]['section_num'] = M('course_section')->where(['course_id'=>['in',$course_ids]])->count();
                    }else{
                        $all_banji[$banji_k]['section_num'] = 0;
                    }
                }

                // todo 班级下的班期信息 和 老师信息
                $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id'=>['eq',$banji_v['id']]])->select();// 班期信息
                if ($banji_banci)
                {
                    // 班主任ids
                    $banzhuren_ids = array_map(function ($v){
                        return $v['headmaster_id'];
                    },$banji_banci);
                    // 班次 ids
                    $banci_ids = array_map(function ($v){
                        return $v['id'];
                    },$banji_banci);
                    if ($banci_ids)
                    {
                        $new_paike = (new ScheduleModel())->where(['period_id'=>['in',$banci_ids]])->order('start_time')->find()?:[];// 班次内最早的排课
                        $all_banji[$banji_k]['start_time'] = $new_paike['start_time']?:0;// 班次开始时间
                        $_teacher_id = $new_paike['teacher_id']?:0;// 班次开始时间
                        if ($_teacher_id)
                        {
                            // 老师名字
                            $all_banji[$banji_k]['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$_teacher_id]])->find()['full_name']?:0;
                        }
//                    $all_banji[$banji_k]['banzhuren_id'] = $banci_ids;// 班主任ids
                    }
                }

                // 班级分类
                switch ($banji_v['category'])
                {
                    case 1:
                        $banji[1][] = $all_banji[$banji_k];// 修士考试班
                        break;
                    case 2:
                        $banji[2][] = $all_banji[$banji_k];// 留考校内考班
                        break;
                    case 3:
                        $banji[3][] = $all_banji[$banji_k];// 热门公开课班
                        break;
                    case 4:
                        $banji[4][] = $all_banji[$banji_k];// 签约保录班
                        break;
                    default:
                        $banji[5][] = $all_banji[$banji_k];// 其他班级
                }
            }
        }

        return [
            'banji' => $banji,
        ];
    }

    // 暂放,未用到
    public function getShowCourses_old()
    {

        // 获取要在首页展示的课程
        $show_course = $this->where(['is_show' => ['eq', 1]])->select();
        // 获取课程下的课时数
        if ($show_course) {
            foreach ($show_course as $k => $v) {
                $show_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
            }
        }

        // 获取公开课 (课程)
        $open_course = $this->where(['is_show' => ['eq', 1], 'is_open' => ['eq', 1]])->select();
        // 获取课程下的课时数
        if ($open_course) {
            foreach ($open_course as $k => $v) {
                $open_course[$k]['section_num'] = (new SectionModel())->where(['course_id' => ['eq', $v['id']]])->count();
            }
        }

        // 获取要在首页展示的班级
        $banjis = M('Course')->where(['is_show' => ['eq', 1]])->select();

        // 获取班级下的课程数和课时数
        if ($banjis) {
            foreach ($banjis as $banji_k => $banji_v) {
                // 预留为0 便于前端识别
                $banjis[$banji_k]['start_time'] = 0;
                $banjis[$banji_k]['teacher_name'] = 0;
                // 班级下的课程ids
                $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id' => ['eq', $banji_v['id']]])->select();

                if ($_course_ids) {
                    // 二维转一维
                    $course_ids = array_map(function ($v) {
                        return $v['section_cate_id'];
                    }, $_course_ids);
                    // 班级下的课程数
                    $banjis[$banji_k]['course_num'] = count($course_ids);
                    // 班级下所有课程的总课时数
                    if ($course_ids) {
                        $banjis[$banji_k]['section_num'] = M('course_section')->where(['course_id' => ['in', $course_ids]])->count();
                    } else {
                        $banjis[$banji_k]['section_num'] = 0;
                    }
                }

                // todo 班级下的班期信息 和 老师信息
                $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id' => ['eq', $banji_v['id']]])->select();// 班期信息
                if ($banji_banci) {
                    // 班主任ids
                    $banzhuren_ids = array_map(function ($v) {
                        return $v['headmaster_id'];
                    }, $banji_banci);
                    // 班次 ids
                    $banci_ids = array_map(function ($v) {
                        return $v['id'];
                    }, $banji_banci);
                    if ($banci_ids) {
                        $new_paike = (new ScheduleModel())->where(['period_id' => ['in', $banci_ids]])->order('start_time')->find() ?: [];// 班次内最早的排课
                        $banjis[$banji_k]['start_time'] = $new_paike['start_time'] ?: 0;// 班次开始时间
                        $_teacher_id = $new_paike['teacher_id'] ?: 0;// 班次开始时间
                        if ($_teacher_id) {
                            // 老师名字
                            $banjis[$banji_k]['teacher_name'] = (new TeacherModel())->where(['role_id' => ['eq', $_teacher_id]])->find()['full_name'] ?: 0;
                        }
//                    $banjis[$banji_k]['banzhuren_id'] = $banci_ids;// 班主任ids
                    }

                }

            }
        }

        // 获取内部公开课 (班级)
        $open_banjis = M('Course')->where(['is_show' => ['eq', 1], 'is_open' => ['eq', 1]])->select();
        // 获取班级下的课程数和课时数
        if ($open_banjis) {
            foreach ($open_banjis as $banji_k => $banji_v) {
                // 预留为0 便于前端识别
                $open_banjis[$banji_k]['start_time'] = 0;
                $open_banjis[$banji_k]['teacher_name'] = 0;
                // 班级下的课程ids
                $_course_ids = M('banji_kecheng')->field('section_cate_id')->where(['course_id' => ['eq', $banji_v['id']]])->select();
                if ($_course_ids) {
                    // 二维转一维
                    $course_ids = array_map(function ($v) {
                        return $v['section_cate_id'];
                    }, $_course_ids);
                    // 班级下的课程数
                    $open_banjis[$banji_k]['course_num'] = count($course_ids);
                    // 班级下所有课程的总课时数
                    if ($course_ids) {
                        $open_banjis[$banji_k]['section_num'] = M('course_section')->where(['course_id' => ['in', $course_ids]])->count();
                    } else {
                        $open_banjis[$banji_k]['section_num'] = 0;
                    }
                }

                // todo 班级下的班期信息 和 老师信息
                $banji_banci = (new PeriodModel())->field('id,headmaster_id')->where(['course_id' => ['eq', $banji_v['id']]])->select();// 班期信息
                if ($banji_banci) {
                    // 班主任ids
                    $banzhuren_ids = array_map(function ($v) {
                        return $v['headmaster_id'];
                    }, $banji_banci);
                    // 班次 ids
                    $banci_ids = array_map(function ($v) {
                        return $v['id'];
                    }, $banji_banci);
                    if ($banci_ids) {
                        $new_paike = (new ScheduleModel())->where(['period_id' => ['in', $banci_ids]])->order('start_time')->find() ?: [];// 班次内最早的排课
                        $open_banjis[$banji_k]['start_time'] = $new_paike['start_time'] ?: 0;// 班次开始时间
                        $_teacher_id = $new_paike['teacher_id'] ?: 0;// 班次开始时间
                        if ($_teacher_id) {
                            // 老师名字
                            $open_banjis[$banji_k]['teacher_name'] = (new TeacherModel())->where(['role_id' => ['eq', $_teacher_id]])->find()['full_name'] ?: 0;
                        }
//                    $banjis[$banji_k]['banzhuren_id'] = $banci_ids;// 班主任ids
                    }
                }

            }
        }

        return [
            'banji' => $banjis,
            'course' => $show_course,
            'open_banji' => $open_banjis,
            'open_course' => $open_course,
        ];
    }

}