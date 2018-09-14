<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Model\CourseBespeakModel;
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
    public function index ()
    {

//        // 获取课程类型的产品
//        $type_of_course_product = (new CourseBespeakModel())->where(['category_id'=>['eq',1],['on_sale'=>['eq','是']]])->select();
//        // 将产品列表转换为IDS
//        $type_of_course_product_ids = array_map(function($v){
//            return $v['product_id'];
//        },$type_of_course_product);
//        // 获取产品和课程的关联关系
//        $product_has_courses = M('CourseProduct')->where(['product_id'=>['in',$type_of_course_product_ids]])->group('course_id')->select();
//        // 获取课程IDS
//        $product_has_courses_ids = array_map(function($v){
//            return $v['course_id'];
//        },$product_has_courses);
//        // 获取课程列表
//        $courses = M('Course')->where(['id'=>['in',$product_has_courses_ids]])->select();
//        // 获取课程中包含的课时
//        foreach ($courses as $k => $v)
//        {
//            $courses[$k]['has_section'] = (new SectionModel())->where(['course_id'=>['eq',$v['id']]])->select();
//        }
//
//        echo "<pre>";
//        var_dump($courses);exit();







        $s_id           =   session('_student')['id'];

        // TODO 购买产品、课程列表、课表信息
        $schedule       =   [];
        $course         =   [];
        $product        =   [];

        $studentModel           =   new StudentModel();
        $periodModel            =   new PeriodModel();
        $letterModel            =   new LetterModel();

        $scheduleData           =   $studentModel->studentSchedule($s_id);

        $now                    =   time();

        // 学生信息
        $studentInfo            =   $studentModel->field('password,remark',true)->relation('profile')->find($s_id);

        // 未读信息数量
        $letterUnreadCount      =   $letterModel->unreadCount(['student_id'=>['eq',$s_id]]);

        //最新的站内信
        $newestLetter = $letterModel->getNewestLetter(['student_id'=>['eq',$s_id]]);


        // 课时信息
        if ($scheduleData)
        {

            foreach ($scheduleData as $key => $value){

                // 8-27 如果当前课程有房间号码,添加进入房间的链接 TODO wait
                if ($value['serial'])
                {
                    $_tk_send_url = C('TK_ROOM_URL')?:'http://global.talk-cloud.net/WebAPI/';
                    $send_url = $_tk_send_url.'entry';// 接口请求地址
                    $send_url .= '?domain='.C('TK_ROOM_DOMAIN');// 公司域名
                    // auth 值为 MD5(key + ts + serial + usertype)
                    $send_url .= '&auth='.md5(C('TK_ROOM_KEY') . time() . $value['serial'] . 2);// 令牌
                    $send_url .= '&usertype=2';// 用户类型 2=学员
                    $send_url .= '&ts='.time();// 时间戳
                    $send_url .= '&serial='.$value['serial'];// 房间号码
                    $send_url .= '&username='.$studentInfo['realname']?:'无名';// 用户姓名
                    $send_url .= '&pid='.$studentInfo['code']?:'0';// 用户ID  (小莺学员学号)
                    $value['serial'] = $send_url;
                }
                // 8-27 end
		  //获取当前的时间
                 $current_time =time();
                $start_time=strtotime($value['start_time']);
           
                if(abs($current_time-$start_time)>7200){
                    $value['is_show']=0;
                }else{
                    $value['is_show']=1;
                }

                

                // 8-29 获取当前课时的录播视屏 TODO wait
                //$value['video_path'] = (new SectionModel())->find($value['section_id'])['video_path']?:'';
               // $sectionInfo = $sectionModel->field('video_path')->where('id='.$value['section_id'])->find();
//                $sql=;
               // $sectionInfo = M('course_section')->field('video_path')->where('id='.$value['section_id'])->find();
                $sectionInfo=M()->query('Select video_path from education.course_section where id='.$value['section_id'].' limit 1');

                $value['video_path'] =$sectionInfo?$sectionInfo[0]['video_path']:'';
                // 8-29 end
//                dump( M()->getLastSql());
//                dump( $value['video_path']);
//                die;

                $value['week']=date("w",strtotime($value['start_time']));

                // 排课状态  是否已经结束
//                $value['status']        =   ($now>$value['stamp']) ? -1 : 1;
                $value['status']        =   ($now > strtotime($value['end_time'])) ? -1 : 1;
                $schedule[date('Y.m.d',$value['stamp'])][]    =   $value;
            }
        }

        $res=$studentModel->getValidCourse($s_id);
        // 课程
        // 班级
        $period                 =   $periodModel->period_list(['s.id'=>['eq',$s_id]]);

        foreach ($period as $key=> $p){
            if(!in_array($p['course_id'],$res)){
                unset($period[$key]);
            }
        }
        $crm_domain             =   C('CRM_DOMAIN');
        $period                 =   array_map( function($p) use($crm_domain){
            $p['course_pic']        =   $crm_domain.substr( $p['course_pic'],1 );
            return $p;
        },$period );
        // 排课

        $this->ajaxReturn( [
            'result'                    =>  true,
            'schedule'                  =>  $schedule,
            'period'                    =>  $period,
            'letterUnreadCount'         =>  $letterUnreadCount,
            'info'                      =>  $studentInfo,
            'newestLetter'              => $newestLetter,
            'desc'                      =>  [
                'status:本节课是否已经上过 已结束（-1） 即将开始（1），signin:是否签到 请假（-7） 未签到（0，null） 已签到（1）'
            ],
        ] );
    }

    /**
     * 课程回顾
     * 8-27 dragon
     */
    public function courseReview()
    {
        // 获取当前用户所有课程,通过课程查找到当前课程所有的已过期的课时,通过课时从拓课云查找当前课时的录制视频列表

        $s_id           =   session('_student')['id'];

        // 获取当前用户所在的课程/班级
        $course_list                 =   (new PeriodModel())->period_list(['s.id'=>['eq',$s_id]]);

        if (!$course_list) $this->ajaxReturn(['result'=>false,'msg'=>'暂未加入任何课程 和 班级']);

        // 将当前用户的节列表转换为IDS
        $course_ids = array_map(function($v){
            return $v['period_id'];
        },$course_list);

        // 将当前用户的课程列表转换为IDS
        $course_tag_ids = array_map(function($v){
            return $v['course_id'];
        },$course_list);

        $end_course_tag_ids = array_unique($course_tag_ids);

        // 获取当前课程下所有分类/标签
//        $tags = M('CourseSectionCate')->where(['course_id'=>['in',$end_course_tag_ids]])->select();

        $_end_course_tag_ids = implode(',',$end_course_tag_ids);
        $tags = M()->query('Select * from education.course_section_cate where course_id in ('.$_end_course_tag_ids.')');

        // 查询当前课程下所有课节
        $_course_ids = implode(',',$course_ids);
        $schedule_list = M()->query('Select * from education.course_schedule where period_id in ('.$_course_ids.')');
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
        foreach($schedule_list as $k => $v) {
            if ($par['cate']) {

                // $sectionInfo = M('course_section')->field('video_path')->where('id='.$value['section_id'])->find();
                $_current_sections_data = M()->query('Select * from education.course_section where cate='.$par['cate']);

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
            $_current_sc_long = M()->query('Select * from education.course_section where id='.$v['section_id'].' limit 1');
            $current_sc_long = $_current_sc_long['duration']?:0;

            if($par['start'] && $par['end']){
                if((strtotime($v['start_time']) < $par['start']) || ((strtotime($v['start_time']) + ($current_sc_long * 60)) > $par['end']))
                {
                    unset($schedule_list[$k]);
                    continue;
                }
            }elseif($par['start']){
                if(strtotime($v['start_time']) < $par['start'])
                {
                    unset($schedule_list[$k]);
                    continue;
                }
            }elseif($par['end']){
                if((strtotime($v['start_time']) + ($current_sc_long * 60)) > $par['end'])
                {
                    unset($schedule_list[$k]);
                    continue;
                }
            }



            // 获取课时标题
//            $schedule_list[$k]['section_name'] = (new SectionModel())->find($v['section_id'])['title'];// 当前课时标题
            $_course_title_data = M()->query('Select * from education.course_section where id='.$v['section_id'].' limit 1');// 当前课时标题
            $schedule_list[$k]['section_name'] = $_course_title_data?$_course_title_data[0]['title']:' - ';// 当前课时标题
            // 获取授课老师名称
//            $schedule_list[$k]['teacher_name'] = (new TeacherModel())->where(['role_id'=>['eq',$v['teacher_id']]])->find()['full_name'];// 当前课时授课老师
            $_teacher_data = M()->query('Select * from mxcrm.mx_user where role_id='.$v['teacher_id'].' limit 1');// 当前课时授课老师
            $schedule_list[$k]['teacher_name'] = $_teacher_data?$_teacher_data[0]['full_name']:' - ';// 当前课时授课老师

            $current_schedule_time_range = $current_sc_long;// 当前课时时长
            $current_schedule_end_time = (strtotime($v['start_time']) + ($current_schedule_time_range * 60));// 当前课时结束时间
            if($current_schedule_end_time > time())
            {
                unset($schedule_list[$k]);
            }else{
                if($v['serial'])
                {
                    // 通过课时中的房间号码,获取房间中的录制视屏
                    $_tk_send_url = C('TK_ROOM_URL')?:'http://global.talk-cloud.net/WebAPI/';
                    $tk_send_data['key'] = C('TK_ROOM_KEY')?:'PGxzTqaSNL0WEWTL';// key
                    $tk_send_data['serial'] = $v['serial'];// 房间号码
//                    var_dump(curlPost($_tk_send_url.'getrecordlist','Content-type:application/x-www-form-urlencoded',$tk_send_data));exit();

                    $current_room_video_list = json_decode(curlPost($_tk_send_url.'getrecordlist','Content-type:application/x-www-form-urlencoded',$tk_send_data)['msg']);

//                    var_dump($current_room_video_list);
//                    var_dump($current_room_video_list->result);

//                    echo "<pre>";
//                    var_dump(curlPost($_tk_send_url.'getrecordlist','Content-type:application/x-www-form-urlencoded',$tk_send_data));exit();

                    if(!$current_room_video_list->result)
                    {
                        $schedule_list[$k]['has_videos'] = $current_room_video_list->recordlist;
                    }else{
                        $schedule_list[$k]['has_videos'] = '';
                    }

                }
            }
        }

//        exit();

        $schedule_list=array_values($schedule_list);

        $this->ajaxReturn( [
            'result'                    =>  true,
            'course_review'            =>  $schedule_list,
            'cate_tag'                  =>  $tags,
        ] );
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
        var_dump($res);exit();
    }

    /**
     * 老师下课回调 学员签到
     */
    public function class_end_callback()
    {
        // 通过回调的 get['serial'] 参数 获取房间号
        $serial_number = intval($_GET['serial']);// 目前房间号都是整型

        // 如果获取不到回调地址的房间号码
        if (!$serial_number)
        {
            // 错误日志
            $err_msg = date('Y-m-d H:i:s',time()).' 下课回调未获取到房间号码,请联系拓课云排查原因.';

            // todo 将 $err_msg 写入错误日志

            return false; // 不执行任何操作
        }

        // 通过房间号码,获取房间中的用户登录登出情况
        $_tk_send_url = C('TK_ROOM_URL')?:'http://global.talk-cloud.net/WebAPI/';
        $tk_send_data['key'] = C('TK_ROOM_KEY')?:'PGxzTqaSNL0WEWTL';// key
        $tk_send_data['serial'] = $serial_number;// 房间号码

        // 发起请求
        $stu_login_status = json_decode(curlPost($_tk_send_url.'getlogininfo','Content-type:application/x-www-form-urlencoded',$tk_send_data)['msg']);

        // 如果请求失败
        if ($stu_login_status->result != 0)
        {
            // 错误日志
            $err_msg = date('Y-m-d H:i:s',time())." 下课回调使用'getlogininfo'接口获取房间用户登录登出信息失败,请按拓课云文档错误码对照表排查原因.房间号:{$serial_number}, 错误码:{$stu_login_status->result}";

            // todo 将 $err_msg 写入错误日志

            return false; // 不执行任何操作
        }

        // 所有用户的登录登出详细信息
        $all_login_info = $stu_login_status->logininfo;

        $stu_list = [];// 有登录登出记录的学员列表

        foreach ($all_login_info as $k => $v)
        {
            // 如果当前用户是学员
            if ($v->userroleid == 2)
            {
                // 当前学员学号
                $user_id = $v->userid;

                // 当前学员进入房间时间
                $user_start_time = $v->entertime;

                // 当前学员离开房间时间
                $user_out_time = $v->outtime;

                // 如果当前学员已经在学员列表中,最终停留时间=本次停留时间+之前的停留时间 (学员可能会有多次登录登出记录)
                if (in_array($user_id,array_keys($stu_list)))
                {
                    $stu_list[$user_id]['in_room_time'] = $stu_list[$user_id]['in_room_time'] + (strtotime($user_out_time) - strtotime($user_start_time));
                }else{
                    // 把 当前学员在房间的停留时间 赋值给 精确的学员列表中
                    $stu_list[$user_id]['in_room_time'] = strtotime($user_out_time) - strtotime($user_start_time);
                }

            }else{
                // 其他角色不操作
                continue;
            }
        }

        // 通过房间号码,获取当前课节时长
        $current_kejie_model = new ScheduleModel();
        $kejie_info = $current_kejie_model->where(['serial'=>['eq',$serial_number]])->find();
        $kejie_re_section_id = $current_kejie_model->where(['serial'=>['eq',$serial_number]])->find()['section_id'];
        $section_model = new SectionModel();
        $section_length = $section_model->find($kejie_re_section_id)['duration']?:100;// 课节时长缺省值为100,以防万一

        // 如果学员在当前房间累积在线时间不小于 当前房间时长n分钟-20分钟 则请求学员签到的接口
        foreach ($stu_list as $stu_k => $stu_v) {
            // PS: 课时时长单位是分钟,学员在教室停留的时长单位是秒,所以$section_length需要*60
            if ($stu_v['in_room_time'] > (60 * $section_length))
            {
                // 请求学员签到的接口 学员学号=$stu_k 课节编号=$kejie_info['id']
                $singIn_send_data['student_code'] = $stu_k;
                $singIn_send_data['schedule_id'] = $kejie_info['id'];
                $stu_login_status = json_decode(curlPost("http://crm.xiaoying.net/index.php?m=signIn&a=signin_in",'Content-type:application/x-www-form-urlencoded',$singIn_send_data));
                // todo 签到请求后,将 $stu_login_status 写入签到记录日志
            }
        }
    }

    public function profile ()
    {
        $model          =   new StudentModel();

        $info           =   $model->field('mobile,password',true)->relation('profile')->find(session('_student.id'));

        $this->ajaxReturn([
            'result'    =>  true,
            'info'      =>  $info
        ]);
    }

    public function setting ()
    {
        try{
            if( IS_POST && IS_AJAX ){
                $params             =   I('post.');
                
                $s_id               =   (int)session('_student.id');

                if ($params['realname']) M('Students')->where('id='.$s_id)->save(['realname'=>$params['realname']]);
                if ($params['email'])
                {
                    // 绑定邮箱
                    M('Students')->where('id='.$s_id)->save(['email'=>$params['email']]);
                    // TODO 发送邮件通知
                }

                $profileModel       =   new ProfileModel();
                C('TOKEN_ON',false);
                if( $data=$profileModel->field('nickname,address,bind_mobile,sex')->create($params,1) ){
                    // 开启事务
                    $profileModel->startTrans();
                    $data['student_id']     =   $s_id;
                    if( $profileModel->find($s_id) ){
                        $profileModel->save($data)===false && E($profileModel->getError());
                    }else{
                        $profileModel->add($data)===false && E($profileModel->getError());
                    }
                    // 提交
                    $profileModel->commit();
                    $this->ajaxReturn(['result'=>true,'debug'=>$data]);
                }
                E( $profileModel->getError() );
            }
            E('非法请求',403);
        }catch (Exception $e){
            $e->getCode()==403||$profileModel->rollback();
            $this->ajaxReturn( [
                'result'            =>  false,
                'error'             =>  $e->getMessage()
            ] );
        }
    }

    public function uploadHeadpic ($fileKey='file')
    {
        try{
            if( $_FILES[$fileKey] && $_FILES[$fileKey]['error']==0 ){
                $info=$this->uploadOne( $fileKey, 'headpic/' );
                $info===false && E($this->error);
                $data['headpic']        =   './Upload/'.$info['savepath'].$info['savename'];
                $data['student_id']     =   (int)session('_student.id');
                $model                  =   new ProfileModel();
                if( $model->find($data['student_id']) ){
                    $model->where(['student_id'=>['eq',$data['student_id']]])->setField('headpic',$data['headpic'])===false && E($model->getError());
                }else{
                    $model->add($data)===false && E($model->getError());
                }

                $this->ajaxReturn( ['result'=>true] );
            }
            E('图片信息有误');
        }catch (Exception $e){
            $this->ajaxReturn([
                'result'        =>  false,
                'error'         =>  $e->getMessage(),
            ]);
        }
    }
}