<?php

// +----------------------------------------------------------------------
// | 路由配置 ： 规则路由、静态路由
// +----------------------------------------------------------------------

return [
    'URL_ROUTE_RULES'       =>  [
        // Home




        // 定时任务
        'crontab/:type/[:time\d]'       =>  'Crontab/:1/index',
    ],


    'URL_MAP_RULES'         =>  [
        'xiaoying'                         =>  'Xiaoying/View/index',     //课程
        'studyingabroad'       =>  'Studying/View/index', //留学申请
        'studyingabroad/result'       =>  'Studying/View/result', //留学申请

        'news'       =>  'Zixun/View/index', //资讯列表
        'news/detail'       =>  'Zixun/View/detail', //资讯详情

        'about'       =>  'About/View/index', //资讯详情


        'school/jpyy'       =>  'School/View/jpyy', //日本语言学校
        'school/jp'       =>  'School/View/jp', //日本大学
        'school/kr'       =>  'School/View/kr', //韩国大学
        'school/sg'       =>  'School/View/sg', //韩国大学
    	'school/jp/detail'       =>  'School/View/JpDetail', //学校详情
        'advantage'     =>  'Advantage/View/index',
        'onlinemall'     =>  'OnlineMall/View/index',
        'api/getgoods'     =>  'OnlineMall/Index/getProduct',// 获取商品

        'api/current_sch_video'            =>  'Home/Student/getPastLiveVideo',   // 指定课节的往期直播视频资源
        'api/banji_select'            =>  'Home/Student/banjiSelect',   // 班级选择 - 所有班级->班次->排课->房间资源
        'api/current_per_sch'            =>  'Home/Student/getCurrentBanciPaike',   // 当前班次的排课信息


        '/'                    	 	=>  'Mall/View/index',     //课程
        // '/'                         =>  'Open/View/index',      // 主页 公开课
        'open'                      =>  'Open/View/index',      // 主页 公开课
        'open/detail'               =>  'Open/View/detail',     //详情
        'update'                      =>  'Update/View/index',     //课程

        'mall'                     	=>  'Mall/View/index',     //课程
        'mall/detail'              =>  'Mall/View/detail',     //课程
        'api/mall'                 	=>  'Mall/Index/search',   //课程数据

        'api/mycourse'            =>  'Mall/Index/mycourse',   //我的课程

        'api/opencourse'          =>  'Mall/Index/getOpenCourse', // 公开课
        'api/zhibocourse'          =>  'Mall/Index/getZhiboCourse', // 直播课
        'api/xiaoneikaocourse'          =>  'Mall/Index/getXiaoneiCourse', // 校内考课
        'api/baoluban'          =>  'Mall/Index/getBaoluBan', // 保录班
        'api/banji_detail'      =>  'Mall/Index/banjiDetail', // 班级详情页
        'api/course_detail'     =>  'Mall/Index/courseDetail', // 课程详情页
        'api/live_video'     =>  'Mall/Index/getLiveVideo', // 课程详情页
        'api/mate'               =>  'Mall/Index/getDocList', // 资料
        'api/matecate'          =>  'Mall/Index/getDocCate', // 资料分类
        'api/tuijian_banji'          =>  'Mall/Index/tuijianBanji', // 推荐班级
        'api/search_all'          =>  'Mall/Index/searchAll', // 整站搜索

        // 拓课云 下课回调
        'api/stu_room_singin'          =>  'Home/Index/class_end_callback', // 拓课云下课回调 学员自动签到

        'teacher'                     =>  'teacher/View/index',     //课程
        'Lessonintor'                     =>  'Lessonintor/View/index',     //课程
        'FreeClass'                     =>  'FreeClass/View/index',     //课程
        'DownLoadCenter'                     =>  'DownLoad/View/index',     //课程

        'zhibo'                       =>  'Zhiboke/View/index',     //课程
        'xiaonei'                     =>  'Xiaoneike/View/index',     //课程
        'baolu'                     =>  'Baoluke/View/index',     //课程
        'result'                     =>  'Result/View/index',     //课程


        'case'                      =>  'Cases/View/index',      // 主页 案例
        'case/thanks'               =>  'Cases/View/thanks',      // 主页 案例
        'case/admission'            =>  'Cases/View/admission',      // 主页 案例
        'plus'                      =>  'Plus/View/index',          //小莺加项目
        'plus/join'                 =>  'Plus/View/join',          //小莺加项目 - 加入
        'plus/experience'           =>  'Plus/View/experience',          //小莺加项目 - 体验
        'case/detail'               =>  'Cases/View/casedetails',      // 主页 案例
        'user'                      =>  'Home/Index/index',
        'test'                      =>  'Home/Index/test',
        'api/isLogin'               =>  'Home/Index/isLogin',

        /* student */
        'api/login'                 =>  'Home/Auth/login',  // 登陆
        'api/logout'                =>  'Home/Auth/logout', // 登出
        'api/reset'                 =>  'Home/Auth/reset', // 密码重置
        'api/profile'               =>  'Home/Student/profile', // 学员信息
        'api/upload/headpic'        =>  'Home/Student/uploadHeadpic', // 上传头像
        'api/student/info'          =>  'Home/Student/index', // 初始化主页数据
        'api/profile/setting'       =>  'Home/Student/setting', // 个人信息修改
        'api/sendmessagecode'       =>  'Home/Auth/sendsms',// 发送短信
        'api/loginbytel'            =>  'Home/Auth/smslogin',// 短信登录验证
        'api/bindmail'              =>  'Home/Bindmail/bindmail',// 绑定邮箱
        'api/sendallmessagecode' => 'Home/Auth/sendallsms', // 发送国外、国内短信
        'api/changephonebycode' => 'Home/Auth/changephonebycode', // 短信修改手机号

        // 8-27 个人中心新增部分功能 dragon
        'api/course/review'          =>  'Home/Student/courseReview', // 课程回顾
        'api/course/bespeak'          =>  'Home/Student/courseBespeak', // 课程预约
        // 8-27 end

        /* letter */
        'api/letter/list'           =>  'Home/Letter/index',// 获取信件列表
        'api/letter/read'           =>  'Home/Letter/read',// 信件标记已读

        'api/myapply'               =>  'Apply/Apply/myApply',  // 我的申请
        'api/mymaterials'           =>  'Apply/Apply/myMaterials',  // 我的材料
        'api/mysample'              =>  'Apply/Apply/myMaterialsSample',  // 我的样本
        'api/studelmaterials'       =>  'Apply/Apply/delCurrentUserMaterials',  // 删除材料
        'api/stuaddmaterials'       =>  'Apply/Apply/stuMaterialsAdd',  // 添加材料


        /* 手机端路由 */
        // 'm'                         =>  'Open/View/mindex', // 主页公开课
        'm'                         =>  'Mobile/Index/classindex', // 主页公开课
        'm/open'                    =>  'Open/View/mindex', // 主页公开课
        'm/open/detail'             =>  'Open/View/mdetail', // 公开课详情

        'm/class'                    =>  'Mobile/Index/classindex', // 主页公开课
        'm/user'                    =>  'Mobile/Index/index', // 个人中心
        'm/login'					=>	'Mobile/Index/login',//登陆页面
        'm/register'				=>	'Mobile/Index/register',//登陆页面
        'm/course'			        =>	'Mobile/Index/course',//我的班级
        'm/course/hour'		        =>	'Mobile/Index/hour',//查看课节
        'm/apply'				    =>	'Mobile/Index/apply',//我的申请
        'm/visa'				    =>	'Mobile/Index/visa',//签证办理
        'm/seo'				        =>	'Mobile/Index/seo',//SEO申请
        'm/material'			    =>	'Mobile/Index/material',//我的材料
        'm/message'			        =>	'Mobile/Index/message',//消息列表
        'm/message/details'	        =>	'Mobile/Index/messagedetails',//消息详情
        'm/set'	                    =>	'Mobile/Index/set',//个人信息


        'm/home'                    =>  'Mobile/Index/home', // 个人中心 改版
        'm/myclass'                =>  'Mobile/Index/myclass', // 我的课程 改版
        'm/myclass/hour'           =>  'Mobile/Index/myclasshour', // 我的课程 改版
        'm/myclass/videocourse'    =>  'Mobile/Index/videocourse', // 我的课程 改版
        'm/myclass/videocourse/sections'    =>  'Mobile/Index/videocoursesections', // 我的课程 改版
        'm/myclass/videoreview'    =>  'Mobile/Index/videoreview', // 我的课程 改版
        'm/myclass/opencourse'    =>  'Mobile/Index/opencourse', // 我的课程 改版
        'm/myclass/opencourse/detail'    =>  'Mobile/Index/opencoursedetail', // 我的课程 改版
        'm/checkkejian'    =>  'Mobile/Index/checkkejian', // 我的课程 改版




        /* 公开课接口路由 */
        'api/open'                  =>  'Open/Index/index',// 列表
        'api/open/detail'           =>  'Open/Index/detail',// 详情
        'api/open/snumIncre'        =>  'Open/Index/snumIncre', // 学习人数递增


        /* 支付路由 */
        'alipay/pay'=>'Home/Payment/aliPayPage',
        'alipay/return'=>'Home/Payment/aliPayReturn',
        'alipay/notify'=>'Home/Payment/aliPayNotify',
        'alipay/qrcode'=>'Home/Payment/qrcode',
    ],
];