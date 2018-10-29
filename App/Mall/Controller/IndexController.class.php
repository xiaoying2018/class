<?php
namespace Mall\Controller;

use Home\Model\CourseBespeakModel;
use Home\Model\CourseModel;
use Home\Model\DocCateModel;
use Home\Model\DocModel;
use Home\Model\ScheduleModel;
use Mall\Model\SectionCateModel;
use Think\Controller;

class IndexController extends Controller
{
    // 整站模糊搜索
    public function searchAll()
    {
        $type = I('post.type');

        $search_name = I('post.name');

        if (!$type || !$search_name) $this->ajaxReturn(['result'=>false,'msg'=>'缺少关键参数','_par'=>['type'=>$type,'search_name'=>$search_name]]);

        switch ($type)
        {
            case 'mate':// 如果搜索资料
                $i_think_type = '资料搜索';
                $model = new DocModel();
                $res = $model->searchByName($search_name);
                break;
            case 'banji':// 如果搜索班级
                $i_think_type = '班级搜索';
                $model = new SectionCateModel();
                $res = $model->searchBanjiByName($search_name);
                break;
            default:// 默认搜索课程
                $i_think_type = '课程搜索';
                $model = new SectionCateModel();
                $res = $model->searchCourseByName($search_name);
                break;
        }

        $this->ajaxReturn(['result'=>true,'data'=>$res,'i_think_type'=>$i_think_type,'_par'=>['type'=>$type,'search_name'=>$search_name]]);

    }


    public function getLiveVideo()
    {
        $id=I('id');
        $schedus=(new ScheduleModel())->where(['section_id'=>['eq',$id]])->order('start_time desc')->select();
        $_tmp_sections=[];
        if(empty($schedus)){
            $this->ajaxReturn(['result'=>false,'msg'=>'没有直播课记录']);
        }else{
            foreach ($schedus as $k=> $v) {
                $serial=$v['serial'];
                if ($serial) {
                    $has_videos = [];// 存放课节视频

                    $_tk_send_url = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';// 接口路径
                    $tk_send_data['key'] = C('TK_ROOM_KEY') ?: 'PGxzTqaSNL0WEWTL';// key
                    $tk_send_data['serial'] = $serial;// 房间号码

                    // 发起请求
                    $current_room_video_list = json_decode(curlPost($_tk_send_url . 'getrecordlist', 'Content-type:application/x-www-form-urlencoded', $tk_send_data)['msg']);
                    // 返码正常(0为正常)
                    if (!$current_room_video_list->result) $has_videos = $current_room_video_list->recordlist;
                    $_tmp_sections['live_path'][$k]=$has_videos;
                }else{
                    continue;
                }
            }
        }
        $this->ajaxReturn(['result'=>true,'data'=>$_tmp_sections]);
    }

    // mall 所有班级/课程 列表页数据 (目前是首页)
    public function search()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getShowCourses();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // 班级详情
    public function banjiDetail()
    {
        $id = I('post.id');

        if (!$id) $this->ajaxReturn(['result'=>false,'msg'=>'缺少关键参数.']);

        $course_model = new SectionCateModel();

        $res = $course_model->getBanjiInfo($id);

        if (!$res) $this->ajaxReturn(['result'=>false,'msg'=>'班级不存在.']);

        $tuijian_banji = $course_model->getTuijianBanji();

        $this->ajaxReturn(['result'=>true,'data'=> $res]);
    }

    // 推荐班级
    public function tuijianBanji()
    {
        $course_model = new SectionCateModel();

        $tuijian_banji = $course_model->getTuijianBanji();

        $this->ajaxReturn(['result'=>true,'data'=> $tuijian_banji]);
    }

    // 课程详情
    public function courseDetail()
    {
        $id = I('post.id');

        if (!$id) $this->ajaxReturn(['result'=>false,'msg'=>'缺少关键参数.']);

        $course_model = new SectionCateModel();

        $res = $course_model->getCourseInfo($id);

        if (!$res) $this->ajaxReturn(['result'=>false,'msg'=>'课程不存在.']);

        $this->ajaxReturn(['result'=>true,'data'=> $res]);
    }

    // 资料数据
    public function getDocList()
    {
        // 实例化模型
        $doc_model = new DocModel();

        // 获取资料
        $doc_list = $doc_model->getMates();

        $this->ajaxReturn(['result'=>true,'data'=>$doc_list]);
    }

    // 资料分类
    public function getDocCate()
    {
        
        // 实例化模型
        $cate_model = new DocCateModel();

        // 获取资料分类列表
        $cates = $cate_model->getCates();

        $this->ajaxReturn(['result'=>true,'data'=>$cates]);
    }

    // FreeClass 公开课 频道页数据
    public function getOpenCourse()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getOpenCourse();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // xiaonei 校内考 频道页数据
    public function getXiaoneiCourse()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getXiaoneiCourse();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // zhibo 直播课 频道页数据
    public function getZhiboCourse()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getZhiboCourse();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // baolu 保录班 频道页数据
    public function getBaoluBan()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getBaoluBan();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // 我的课程 (录播视频回顾)
    public function mycourse()
    {
        $current_stu_id = session('_student')['id'];// 获取当前学员ID

        if (!$current_stu_id) $this->ajaxReturn(['status'=>false,'msg'=>'非法请求']);

        // 927 新增需求 课程课时改为两步请求
        // 课程id
        $course_id = intval(I('post.c_id'));
        // 通过课程ID 获取当前课程的课时内容
        if ($course_id)
        {
            $one_course_model = M('course_section_cate');
            $one_courses = $one_course_model->where(['id'=>['eq',$course_id]])->find();
            // 获取课程下的课时列表
            $one_courses['sections'] = M('course_section')->where(['course_id'=>['eq',$one_courses['id']]])->order('node')->select()?:[];

            $this->ajaxReturn(['status'=>true,'data'=>$one_courses]);
        }
        // 927 end
        
        // 获取当前学员所属班次ids $my_banci
        $stu_per_model = M('period_student');
        $my_banci = $stu_per_model->field('period_id,student_id')->where(['student_id'=>['eq',$current_stu_id]])->select();

        if ($my_banci)
        {
            // 处理班次ids
            $my_banci_ids = array_map(function ($v){
                return $v['period_id'];
            },$my_banci);
            if ($my_banci_ids)
            {
                // 通过班次 获取班级
                $banci_model = M('course_period');
                $my_banji = $banci_model->field('id,name,course_id')->where(['id'=>['in',$my_banci_ids]])->select();
                if ($my_banji)
                {
                    // 获取到班级的IDS 并去重
                    $my_banji_ids = array_values(array_unique(array_map(function ($v){ return $v['course_id']; },$my_banji)));
                    if ($my_banji_ids)
                    {
                        // 根据班级IDS 获取班级内所包含的课程IDS
                        $banji_kecheng_rel_model = M('banji_kecheng');
                        $rel_courses = $banji_kecheng_rel_model->where(['course_id'=>['in',$my_banji_ids]])->select();
                        if ($rel_courses)
                        {
                            // 获取到课程的IDS 并去重
                            $my_course_ids = array_values(array_unique(array_map(function ($v){ return $v['section_cate_id']; },$rel_courses)));
                            if ($my_course_ids)
                            {
                                // 根据课程 IDS 获取课程列表
                                $course_model = M('course_section_cate');
                                $courses = $course_model->where(['id'=>['in',$my_course_ids]])->select();
                                if ($courses)
                                {
                                    // 获取课程下的课时列表
                                    foreach($courses as $k => $v)
                                    {
                                        $courses[$k]['sections'] = M('course_section')->where(['course_id'=>['eq',$v['id']]])->order('node')->select()?:[];
                                    }

                                    $this->ajaxReturn(['status'=>true,'data'=>$courses]);
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->ajaxReturn(['status'=>false,'msg'=>'No course.']);// 未加入任何班次
    }
    
}