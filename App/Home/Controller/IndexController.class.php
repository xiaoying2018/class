<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Model\ScheduleModel;
use Home\Model\StudentModel;
use Think\Controller;
use Think\Controller\RpcController;

class IndexController extends Controller
{
    public function index()
    {
        $this->display();
    }

    /**
     * 老师下课回调 学员签到
     */
    public function class_end_callback()
    {
        header("Content-type: text/html; charset=utf-8");

        // 通过回调的 get['serial'] 参数 获取房间号
        $serial_number = intval($_GET['serial']);// 目前房间号都是整型

        // 如果获取不到回调地址的房间号码
        if (!$serial_number)
        {
            // 错误日志
            $err_msg = date('Y-m-d H:i:s',time()).' 下课回调未获取到房间号码,请联系拓课云排查原因.';

            // todo 将 $err_msg 写入错误日志
            echo "<pre>";
            var_dump($err_msg);exit();

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
            echo "<pre>";
            var_dump($err_msg);exit();

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
                echo "<pre> success! ";
                var_dump($singIn_send_data);
                var_dump($stu_login_status);
            }
        }
    }

    public function test ()
    {
        exit('-...');
        $model      =   new StudentModel();
        $info       =   $model->find(1);
        if( session('?_student') )
            return ;


        session('_student', $info);

        dump( $info );

    }

    public function isLogin ()
    {
        $is_login               =   session('?_student');
        $result                 =   [
            'result'    =>  $is_login,
            'code'      =>  $is_login ? 200 : 403,
        ];

        $this->ajaxReturn( $result );
    }

}