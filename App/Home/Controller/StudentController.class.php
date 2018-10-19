<?php

namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Model\CourseBespeakModel;
use Home\Model\CourseModel;
use Home\Model\LetterModel;
use Home\Model\PeriodModel;
use Home\Model\ProfileModel;
use Home\Model\StudentModel;
use Home\Model\ScheduleModel;
use Home\Model\SectionModel;
use Home\Model\TeacherModel;
use Think\Exception;

class StudentController extends BaseController
{
    /**
     * 学员个人中心 我的课程/班级
     */
    public function index()
    {
        $s_id = session('_student')['id'];

        // TODO 购买产品、课程列表、课表信息
        $schedule = [];
        $course = [];
        $product = [];

        $studentModel = new StudentModel();
        $periodModel = new PeriodModel();
        $letterModel = new LetterModel();

        $scheduleData = $studentModel->studentSchedule($s_id);

        $now = time();

        // 学生信息
        $studentInfo = $studentModel->field('password,remark', true)->relation('profile')->find($s_id);

        // 未读信息数量
        $letterUnreadCount = $letterModel->unreadCount(['student_id' => ['eq', $s_id]]);

        //最新的站内信
        $newestLetter = $letterModel->getNewestLetter(['student_id' => ['eq', $s_id]]);

        // 课时信息
        if ($scheduleData) {

            foreach ($scheduleData as $key => $value) {

//                重新查询学员签到情况，判断之前的条件是否正确
//                $qd_model = M('schedule_signin');
//                $qd_status = $qd_model->where(['schedule_id'=>['eq',$value['schedule_id']],'student_id'=>['eq',$value['stu_id']]])->find()['status']?:0;
//                $value['signin_status'] = $qd_status;

                // 8-27 如果当前课程有房间号码,添加进入房间的链接
                if ($value['serial']) {

                    $_tk_send_url = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';
                    $send_url = $_tk_send_url . 'entry';// 接口请求地址
                    $send_url .= '?domain=' . C('TK_ROOM_DOMAIN');// 公司域名
                    // auth 值为 MD5(key + ts + serial + usertype)
                    $send_url .= '&auth=' . md5(C('TK_ROOM_KEY') . time() . $value['serial'] . 2);// 令牌
                    $send_url .= '&usertype=2';// 用户类型 2=学员
                    $send_url .= '&ts=' . time();// 时间戳
                    $send_url .= '&serial=' . $value['serial'];// 房间号码
                    $send_url .= '&username=' . $studentInfo['realname'] ?: '无名';// 用户姓名
                    $send_url .= '&pid=' . $studentInfo['code'] ?: '0';// 用户ID  (小莺学员学号)
                    $value['serial'] = $send_url;
                }
                // 8-27 end
                // 获取当前的时间
                $current_time = time();
                $start_time = strtotime($value['start_time']);

                if (abs($current_time - $start_time) > 7200) {
                    $value['is_show'] = 0;
                } else {
                    $value['is_show'] = 1;
                }

                // 8-29 获取当前课时的录播视屏 TODO wait
                //$value['video_path'] = (new SectionModel())->find($value['section_id'])['video_path']?:'';
                // $sectionInfo = $sectionModel->field('video_path')->where('id='.$value['section_id'])->find();
//                $sql=;
                // $sectionInfo = M('course_section')->field('video_path')->where('id='.$value['section_id'])->find();
                if ($value['section_id']) {
                    $sectionInfo = M()->query('Select video_path,course_id from education.course_section where id=' . $value['section_id'] . ' limit 1');

                    $value['kecheng_id'] = $sectionInfo ? $sectionInfo[0]['course_id'] : '';
                    $value['video_path'] = $sectionInfo ? $sectionInfo[0]['video_path'] : '';
                } else {
                    $value['video_path'] = '';
                }
                // 8-29 end
//                dump( M()->getLastSql());
//                dump( $value['video_path']);
//                die;

                $value['week'] = date("w", strtotime($value['start_time']));

                // 排课状态  是否已经结束
//                $value['status']        =   ($now>$value['stamp']) ? -1 : 1;
                $value['status'] = ($now > strtotime($value['end_time'])) ? -1 : 1;
                $schedule[date('Y.m.d', $value['stamp'])][] = $value;
            }
        }

        //$res=$studentModel->getValidCourse($s_id);

        // 课程
        // 班级
        $period = $periodModel->period_list(['s.id' => ['eq', $s_id]]);


//        foreach ($period as $key=> $p){
//            if(!in_array($p['course_id'],$res)){
//                unset($period[$key]);
//            }
//        }
        $crm_domain             =   C('CRM_DOMAIN');
        $period                 =   array_map( function($p) use($crm_domain){
            $p['course_pic']        =   $crm_domain.substr( $p['course_pic'],1 );

            return $p;
        }, $period);
        // 排课

        $this->ajaxReturn([
            'result' => true,
            'schedule' => $schedule,
            'period' => $period,
            'letterUnreadCount' => $letterUnreadCount,
            'info' => $studentInfo,
            'newestLetter' => $newestLetter,
            'desc' => [
                'status:本节课是否已经上过 已结束（-1） 即将开始（1），signin:是否签到 请假（-7） 未签到（0，null） 已签到（1）'
            ],
        ]);
    }

    /**
     * 获取当前班次的排课信息
     * par: per_id 班次编号
     * res: 当前班次排课信息以及TK-Cloud的相关资源
     * aut: Dragon
     */
    public function getCurrentBanciPaike()
    {
        // 获取当前学员信息
        $s_id = session('_student')['id'];

        $studentModel = new StudentModel();

        $studentInfo = $studentModel->field('password,remark', true)->relation('profile')->find($s_id);

        // 获取班次ID 并检测是否合法
        $per_id = intval(I('post.period_id'));

        if (!$per_id) $this->ajaxReturn(['result' => false, 'msg' => '非法请求']);

        // 获取班次信息
        $banci_model = new PeriodModel();

        $banci_info = $banci_model->find($per_id);

        if (!$banci_info) $this->ajaxReturn(['result' => false, 'msg' => '非法请求']);

        $banci_info['paike'] = []; // 排课列表

        // 获取班次内所有排课信息 和 课时名称+课时时长
        $paike_model = new ScheduleModel();

        $paike_list = $paike_model->where(['period_id'=>['eq',$per_id]])->select();

        if (!$paike_list) $this->ajaxReturn(['result' => true, 'data' => $banci_info]);

        $now = time();

        foreach ($paike_list as $k => $v)
        {
            $paike_list[$k]['homework'] = '';
            $paike_list[$k]['has_doc'] = [];
            $paike_list[$k]['serial_has_videos'] = [];

            // 获取当前排课的课后作业
            if ($v['homework'])
            {
                $paike_list[$k]['homework'] = M('material')->where(['id'=>['eq'=>$v['homework']]])->find()['path']?:'';
            }

            if ($v['section_id'])
            {
                $section_model = new SectionModel();
                $section_info = $section_model->find($v['section_id']);
                // 获取课节时长
                $paike_list[$k]['duration'] = $section_info['duration']?:0;
                // 课节结束时间
                $paike_list[$k]['end_time'] = date('Y-m-d H:i:s',strtotime($v['start_time']) + $section_info['duration']);
                // 课节名称
                $paike_list[$k]['section_name'] = $section_info['name']?:0;
                // 课节标题
                $paike_list[$k]['title'] = $section_info['title']?:0;
            }
            
            if ($v['serial'])
            {
                // 学员是否签到
                $paike_list[$k]['signin_status'] = M('schedule_signin')->where(['schedule_id'=>['eq'=>$v['id']],['student_id'=>['eq',$studentInfo['id']]]])->select()?1:0;

                // 获取当前排课在TK-Cloud的课件
                $has_doc_res = M('schedule_document')->where(['schedule_id'=>['eq',$v['id']]])->select();

                if ($has_doc_res)
                {
                    $has_doc_ids = array_map(function ($v){
                        return $v['document_id'];
                    },$has_doc_res);

                    $paike_list[$k]['has_doc'] = M('course_document')->where(['id'=>['in',$has_doc_ids],'is_delete'=>['eq',1]])->select();
                }

                // 获取当前排课在TK-Cloud的往期直播视频
                $_tk_send_url_for_video = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';// 接口路径
                $tk_send_data['key'] = C('TK_ROOM_KEY') ?: 'PGxzTqaSNL0WEWTL';// key
                $tk_send_data['serial'] = $v['serial'];// 房间号码

                // 发起请求
                $current_room_video_list = json_decode(curlPost($_tk_send_url_for_video . 'getrecordlist', 'Content-type:application/x-www-form-urlencoded', $tk_send_data)['msg']);

                // 返码正常(0为正常) 成功存储视频资源列表
                if (!$current_room_video_list->result) $paike_list[$k]['serial_has_videos'] = $current_room_video_list->recordlist;

                // 进入教室的链接
                $_tk_send_url = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';
                $send_url = $_tk_send_url . 'entry';// 接口请求地址
                $send_url .= '?domain=' . C('TK_ROOM_DOMAIN');// 公司域名
                // auth 值为 MD5(key + ts + serial + usertype)
                $send_url .= '&auth=' . md5(C('TK_ROOM_KEY') . time() . $v['serial'] . 2);// 令牌
                $send_url .= '&usertype=2';// 用户类型 2=学员
                $send_url .= '&ts=' . time();// 时间戳
                $send_url .= '&serial=' . $v['serial'];// 房间号码
                $send_url .= '&username=' . $studentInfo['realname'] ?: '无名';// 用户姓名
                $send_url .= '&pid=' . $studentInfo['code'] ?: '0';// 用户ID  (小莺学员学号)
                $value['serial'] = $send_url;

                // 排课状态  是否已经结束
                $paike_list[$k]['status'] = ($now > strtotime($value['end_time'])) ? -1 : 1;

            }



        }

        $banci_info['paike'] = $paike_list; // 排课列表重新赋值


//        header("Content-type: text/html; charset=utf-8");
//        echo "<pre>";
//        var_dump($banci_info);exit();

        $this->ajaxReturn([
            'result' => true,
            'data' => $banci_info
        ]);




//        $s_id = session('_student')['id'];
//
//        // 课程列表
//        $schedule = [];
//
//        $studentModel = new StudentModel();
//
//        $scheduleData = $studentModel->studentSchedule($s_id);
//
//        $now = time();
//
//        // 学生信息
//        $studentInfo = $studentModel->field('password,remark', true)->relation('profile')->find($s_id);
//
//        // 课时信息
//        if ($scheduleData) {
//
//            foreach ($scheduleData as $key => $value) {
//
//                // 1015 希望往期直播视频一次性查出,无畏卡慢顿...
//                $value['serial_has_videos'] = [];// 存放往期直播视频资源
//
//                $_tk_send_url_for_video = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';// 接口路径
//                $tk_send_data['key'] = C('TK_ROOM_KEY') ?: 'PGxzTqaSNL0WEWTL';// key
//                $tk_send_data['serial'] = $value['serial'];// 房间号码
//
//                // 发起请求
//                $current_room_video_list = json_decode(curlPost($_tk_send_url_for_video . 'getrecordlist', 'Content-type:application/x-www-form-urlencoded', $tk_send_data)['msg']);
//
//                // 返码正常(0为正常) 成功存储视频资源列表
//                if (!$current_room_video_list->result) $value['serial_has_videos'] = $current_room_video_list->recordlist;
//                // 1015 end
//
//
//                // 1016 课节文档一次性查出
//                $has_doc_list = [];// 存放课节文档
//
//                $has_doc_res = M('schedule_document')->where(['schedule_id'=>['eq',$value['schedule_id']]])->select();
//
//                if ($has_doc_res)
//                {
//                    $has_doc_ids = array_map(function ($v){
//                        return $v['document_id'];
//                    },$has_doc_res);
//
//                    $has_doc_list = M('course_document')->where(['id'=>['in',$has_doc_ids]])->select();
//                }
//                $value['has_doc'] = $has_doc_list;
//                // 1016 end
//
//                // 8-27 如果当前课程有房间号码,添加进入房间的链接
//                if ($value['serial']) {
//
//                    $_tk_send_url = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';
//                    $send_url = $_tk_send_url . 'entry';// 接口请求地址
//                    $send_url .= '?domain=' . C('TK_ROOM_DOMAIN');// 公司域名
//                    // auth 值为 MD5(key + ts + serial + usertype)
//                    $send_url .= '&auth=' . md5(C('TK_ROOM_KEY') . time() . $value['serial'] . 2);// 令牌
//                    $send_url .= '&usertype=2';// 用户类型 2=学员
//                    $send_url .= '&ts=' . time();// 时间戳
//                    $send_url .= '&serial=' . $value['serial'];// 房间号码
//                    $send_url .= '&username=' . $studentInfo['realname'] ?: '无名';// 用户姓名
//                    $send_url .= '&pid=' . $studentInfo['code'] ?: '0';// 用户ID  (小莺学员学号)
//                    $value['serial'] = $send_url;
//                }
//                // 8-27 end
//
//                // 获取当前的时间
//                $current_time = time();
//                $start_time = strtotime($value['start_time']);
//
//                if (abs($current_time - $start_time) > 7200) {
//                    $value['is_show'] = 0;
//                } else {
//                    $value['is_show'] = 1;
//                }
//
//                if ($value['section_id']) {
//                    $sectionInfo = M()->query('Select video_path,course_id from education.course_section where id=' . $value['section_id'] . ' limit 1');
//
//                    $value['kecheng_id'] = $sectionInfo ? $sectionInfo[0]['course_id'] : '';
//                    $value['video_path'] = $sectionInfo ? $sectionInfo[0]['video_path'] : '';
//                } else {
//                    $value['video_path'] = '';
//                }
//                // 8-29 end
//
//                $value['week'] = date("w", strtotime($value['start_time']));
//
//                // 排课状态  是否已经结束
//                $value['status'] = ($now > strtotime($value['end_time'])) ? -1 : 1;
//
//                $schedule[date('Y.m.d', $value['stamp'])][] = $value;
//            }
//        }
//
//
////        header("Content-type: text/html; charset=utf-8");
////        echo "<pre>";
////        var_dump($schedule);exit();
//
//        $this->ajaxReturn([
//            'result' => true,
//            'data' => $schedule
//        ]);


    }


    /**
     * 特殊需求: 班级选择
     *
     * DESC: 希望有个特殊的账号登录后可以有个按钮,按钮功能: 点击可见所有班级 -> 点击班级可见当前班级下所有班次 -> 点击班次可见班次内所有排课
     *
     * 需求原文:
     *      1.加一个切换班级按钮，切换到不同签约班级的同学界面
     *      2.这个是自己看的，和学生没有关系的
     */
    public function banjiSelect()
    {
        header('content-type:text/html;charset=utf-8');

        // 学员信息
        $s_id = session('_student')['id'];
        $studentModel = new StudentModel();
        $studentInfo = $studentModel->field('password,remark', true)->relation('profile')->find($s_id);

        // 班级
        $banji_model = new CourseModel();
        $banji_list = $banji_model->getAllBanji();

        // 班次
        if ($banji_list)
        {
            $banci_model = new PeriodModel();
            // 当前班级下的班次列表
            foreach ($banji_list as $bj_k => $bj_v)
            {
                $banci_list = $banci_model->where(['status'=>['eq',1],'course_id'=>['eq',$bj_v['id']]])->order('create_at DESC')->select();

                // 排课
                if ($banci_list)
                {
                    $paike_model = new ScheduleModel();
                    // 当前班次下的排课信息
                    foreach ($banci_list as $bc_k => $bc_v)
                    {
                        $paike_list = $paike_model 
                            ->field('p.*,cs.name section_name')
                            ->join("p LEFT JOIN education.course_section cs ON cs.id = p.section_id")
                            ->where(['period_id'=>['eq',$bc_v['id']]])
                            ->order('start_time')
                            ->select();

                        // 拓课云
                        if ($paike_list)
                        {
                            foreach ($paike_list as $pk_k => $pk_v)
                            {
                                // 获取当前排课/房间的登入链接
                                $_tk_send_url = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';
                                $send_url = $_tk_send_url . 'entry';// 接口请求地址
                                $send_url .= '?domain=' . C('TK_ROOM_DOMAIN');// 公司域名
                                // auth 值为 MD5(key + ts + serial + usertype)
                                $send_url .= '&auth=' . md5(C('TK_ROOM_KEY') . time() . $pk_v['serial'] . 2);// 令牌
                                $send_url .= '&usertype=2';// 用户类型 2=学员
                                $send_url .= '&ts=' . time();// 时间戳
                                $send_url .= '&serial=' . $pk_v['serial'];// 房间号码
                                $send_url .= '&username=' . $studentInfo['realname'] ?: '无名';// 用户姓名
                                $send_url .= '&pid=' . $studentInfo['code'] ?: '0';// 用户ID  (小莺学员学号)
                                $paike_list[$pk_k]['serial_link'] = $send_url;
                                // 获取当前排课/房间的课件

                                // 获取当前排课/房间的往期直播视频
                                $_tk_send_url_for_video = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';// 接口路径
                                $tk_send_data['key'] = C('TK_ROOM_KEY') ?: 'PGxzTqaSNL0WEWTL';// key
                                $tk_send_data['serial'] = $pk_v['serial'];// 房间号码

                                // 发起请求
                                $current_room_video_list = json_decode(curlPost($_tk_send_url_for_video . 'getrecordlist', 'Content-type:application/x-www-form-urlencoded', $tk_send_data)['msg']);

                                // 返码正常(0为正常) 成功存储视频资源列表
                                if (!$current_room_video_list->result) $paike_list[$pk_k]['serial_has_videos'] = $current_room_video_list->recordlist;

                            }
                        }

                        $banci_list[$bc_k]['has_paike'] = $paike_list;
                    }
                }

                $banji_list[$bj_k]['has_banci'] = $banci_list;
            }
        }
        
        $this->ajaxReturn(['status'=>true,'data'=>$banji_list]);
    }

    /**
     * 获取指定课节往期直播视频(拓客云)
     */
    public function getPastLiveVideo()
    {
        // 获取当前课节编号
        $sch_id = intval(I('post.sch_id'));

        if (!$sch_id) $this->ajaxReturn(['result' => false, 'msg' => '操作异常 | 课节编号异常']); // 无效的课节编号

        // 根据课节编号获取课节关联的房间号
        $sch_model = new ScheduleModel();
        $serial = $sch_model->where(['id'=>['eq',$sch_id]])->find()['serial']?:0;

        // 如果房间号码存在,通过房间号,获取房间中的录制视屏
        if ($serial) {

            $has_videos = [];// 存放课节视频

            $_tk_send_url = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';// 接口路径
            $tk_send_data['key'] = C('TK_ROOM_KEY') ?: 'PGxzTqaSNL0WEWTL';// key
            $tk_send_data['serial'] = $serial;// 房间号码

            // 发起请求
            $current_room_video_list = json_decode(curlPost($_tk_send_url . 'getrecordlist', 'Content-type:application/x-www-form-urlencoded', $tk_send_data)['msg']);

            // 返码正常(0为正常)
            if (!$current_room_video_list->result) $has_videos = $current_room_video_list->recordlist;

            // 成功返回视频资源列表
            $this->ajaxReturn(['result' => true, 'data' => $has_videos]);
        }

        $this->ajaxReturn(['result' => false, 'msg' => '操作异常 | 未获取到直播间号码']); // 当前课节未关联到直播间 或 直播间不存在
    }

    /**
     * 课程回顾
     * 8-27 dragon
     */
    public function courseReview()
    {
        // 获取当前用户所有课程,通过课程查找到当前课程所有的已过期的课时,通过课时从拓课云查找当前课时的录制视频列表

        $s_id = session('_student')['id'];

        // 获取当前用户所在的课程/班级
        $course_list = (new PeriodModel())->period_list(['s.id' => ['eq', $s_id]]);

        if (!$course_list) $this->ajaxReturn(['result' => false, 'msg' => '暂未加入任何课程 和 班级']);

        // 将当前用户的节列表转换为IDS
        $course_ids = array_map(function ($v) {
            return $v['period_id'];
        }, $course_list);

        // 将当前用户的课程列表转换为IDS
        $course_tag_ids = array_map(function ($v) {
            return $v['course_id'];
        }, $course_list);

        $end_course_tag_ids = array_unique($course_tag_ids);

        // 获取当前课程下所有分类/标签
//        $tags = M('CourseSectionCate')->where(['course_id'=>['in',$end_course_tag_ids]])->select();

        $_end_course_tag_ids = implode(',', $end_course_tag_ids);
        $tags = M()->query('Select * from education.course_section_cate where course_id in (' . $_end_course_tag_ids . ')');

        // 查询当前课程下所有课节
        $_course_ids = implode(',', $course_ids);
        $schedule_list = M()->query('Select * from education.course_schedule where period_id in (' . $_course_ids . ')');
//        $schedule_list = (new ScheduleModel())->where(['period_id'=>['in',$course_ids]])->select();

        // 获取筛选条件
        $par = I('get.');
        if ($par['start']) $par['start'] = strtotime($par['start']);
        if ($par['end']) $par['end'] = strtotime($par['end']);
//        $par['cate'] = 7;
//        $par['start'] = 1533099988;
//        $par['end'] = 1555461514;
//        
        // echo "<pre>";
        // var_dump($par);

        // 筛选出已经结束的课时 并 为已经结束的课时查询出已有的录制件
        foreach ($schedule_list as $k => $v) {
            if ($par['cate']) {

                // $sectionInfo = M('course_section')->field('video_path')->where('id='.$value['section_id'])->find();
                $_current_sections_data = M()->query('Select * from education.course_section where cate=' . $par['cate']);

                // 将当前用户的课时列表转换为IDS
                $_current_sections_data_ids = array_map(function ($v) {
                    return $v['id'];
                }, $_current_sections_data);

                if (!in_array($v['section_id'], $_current_sections_data_ids)) {
                    unset($schedule_list[$k]);
                    continue;
                }
            }

            // 当前课时的时长
//            $current_sc_long = (new SectionModel())->where('id =' . $v['section_id'])->find()['duration'] ?: 0;
            // TODO 所有SQL 全部换成源生SQL!!! dragon
            $_current_sc_long = M()->query('Select * from education.course_section where id=' . $v['section_id'] . ' limit 1');
            $current_sc_long = $_current_sc_long['duration'] ?: 0;

            if ($par['start'] && $par['end']) {
                if ((strtotime($v['start_time']) < $par['start']) || ((strtotime($v['start_time']) + ($current_sc_long * 60)) > $par['end'])) {
                    unset($schedule_list[$k]);
                    continue;
                }
            } elseif ($par['start']) {
                if (strtotime($v['start_time']) < $par['start']) {
                    unset($schedule_list[$k]);
                    continue;
                }
            } elseif ($par['end']) {
                if ((strtotime($v['start_time']) + ($current_sc_long * 60)) > $par['end']) {
                    unset($schedule_list[$k]);
                    continue;
                }
            }


            // 获取课时标题
//            $schedule_list[$k]['section_name'] = (new SectionModel())->find($v['section_id'])['title'];// 当前课时标题
            $_course_title_data = M()->query('Select * from education.course_section where id=' . $v['section_id'] . ' limit 1');// 当前课时标题
            $schedule_list[$k]['section_name'] = $_course_title_data ? $_course_title_data[0]['title'] : ' - ';// 当前课时标题
            // 获取授课老师名称
//            $schedule_list[$k]['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$v['teacher_id']]])->find()['full_name'];// 当前课时授课老师
            $_teacher_data = M()->query('Select * from mxcrm.mx_user where role_id=' . $v['teacher_id'] . ' limit 1');// 当前课时授课老师
            $schedule_list[$k]['teacher_name'] = $_teacher_data ? $_teacher_data[0]['full_name'] : ' - ';// 当前课时授课老师

            $current_schedule_time_range = $current_sc_long;// 当前课时时长
            $current_schedule_end_time = (strtotime($v['start_time']) + ($current_schedule_time_range * 60));// 当前课时结束时间
            if ($current_schedule_end_time > time()) {
                unset($schedule_list[$k]);
            } else {
                if ($v['serial']) {
                    // 通过课时中的房间号码,获取房间中的录制视屏
                    $_tk_send_url = C('TK_ROOM_URL') ?: 'http://global.talk-cloud.net/WebAPI/';
                    $tk_send_data['key'] = C('TK_ROOM_KEY') ?: 'PGxzTqaSNL0WEWTL';// key
                    $tk_send_data['serial'] = $v['serial'];// 房间号码

                    $current_room_video_list = json_decode(curlPost($_tk_send_url.'getrecordlist','Content-type:application/x-www-form-urlencoded',$tk_send_data)['msg']);

                    if(!$current_room_video_list->result)
                    {

                        $schedule_list[$k]['has_videos'] = $current_room_video_list->recordlist;
                    } else {
                        $schedule_list[$k]['has_videos'] = '';
                    }

                }
            }
        }

//        exit();

        $schedule_list = array_values($schedule_list);

        $this->ajaxReturn([
            'result' => true,
            'course_review' => $schedule_list,
            'cate_tag' => $tags,
        ]);
    }

    /**
     * 课程预约中心
     * 8-27 dragon
     */
    public function courseBespeak()
    {
        // 获取课程产品
        $res = (new CourseBespeakModel())->select();

        echo "<pre>";
        var_dump($res);
        exit();
    }

    public function profile()
    {
        $model = new StudentModel();

        $info = $model->field('mobile,password', true)->relation('profile')->find(session('_student.id'));

        $this->ajaxReturn([
            'result' => true,
            'info' => $info
        ]);
    }

    public function setting()
    {
        try {
            if (IS_POST && IS_AJAX) {
                $params = I('post.');

                $s_id = (int)session('_student.id');

                if ($params['realname']) M('Students')->where('id=' . $s_id)->save(['realname' => $params['realname']]);
                if ($params['email']) {
                    // 绑定邮箱
                    M('Students')->where('id=' . $s_id)->save(['email' => $params['email']]);
                    // TODO 发送邮件通知
                }

                $profileModel = new ProfileModel();
                C('TOKEN_ON', false);
                if ($data = $profileModel->field('nickname,address,bind_mobile,sex')->create($params, 1)) {
                    // 开启事务
                    $profileModel->startTrans();
                    $data['student_id'] = $s_id;
                    if ($profileModel->find($s_id)) {
                        $profileModel->save($data) === false && E($profileModel->getError());
                    } else {
                        $profileModel->add($data) === false && E($profileModel->getError());
                    }
                    // 提交
                    $profileModel->commit();
                    $this->ajaxReturn(['result' => true, 'debug' => $data]);
                }
                E($profileModel->getError());
            }
            E('非法请求', 403);
        } catch (Exception $e) {
            $e->getCode() == 403 || $profileModel->rollback();
            $this->ajaxReturn([
                'result' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function uploadHeadpic($fileKey = 'file')
    {
        try {
            if ($_FILES[$fileKey] && $_FILES[$fileKey]['error'] == 0) {
                $info = $this->uploadOne($fileKey, 'headpic/');
                $info === false && E($this->error);
                $data['headpic'] = './Upload/' . $info['savepath'] . $info['savename'];
                $data['student_id'] = (int)session('_student.id');
                $model = new ProfileModel();
                if ($model->find($data['student_id'])) {
                    $model->where(['student_id' => ['eq', $data['student_id']]])->setField('headpic', $data['headpic']) === false && E($model->getError());
                } else {
                    $model->add($data) === false && E($model->getError());
                }

                $this->ajaxReturn(['result' => true]);
            }
            E('图片信息有误');
        } catch (Exception $e) {
            $this->ajaxReturn([
                'result' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}