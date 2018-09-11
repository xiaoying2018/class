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
    	
        '/'                    	 	=>  'Mall/View/index',     //课程
        // '/'                         =>  'Open/View/index',      // 主页 公开课
        'open'                      =>  'Open/View/index',      // 主页 公开课
        'open/detail'               =>  'Open/View/detail',     //详情

        'mall'                     	=>  'Mall/View/index',     //课程
        'mall/detail'              =>  'Mall/View/detail',     //课程
        'api/mall'                 	=>  'Mall/Index/search',   //课程数据

        'api/opencourse'          =>  'Mall/Index/getOpenCourse', // 公开课
        'api/zhibocourse'          =>  'Mall/Index/getZhiboCourse', // 直播课
        'api/xiaoneikaocourse'          =>  'Mall/Index/getXiaoneiCourse', // 校内考课
        'api/baoluban'          =>  'Mall/Index/getBaoluBan', // 保录班
        'api/banji_detail'      =>  'Mall/Index/banjiDetail', // 班级详情页
        'api/course_detail'     =>  'Mall/Index/courseDetail', // 课程详情页
        'api/mate'               =>  'Mall/Index/getDocList', // 资料
        'api/matecate'          =>  'Mall/Index/getDocCate', // 资料分类
        'api/tuijian_banji'          =>  'Mall/Index/tuijianBanji', // 推荐班级

        'teacher'                     =>  'teacher/View/index',     //课程
        'Lessonintor'                     =>  'Lessonintor/View/index',     //课程
        'FreeClass'                     =>  'FreeClass/View/index',     //课程
        'DownLoadCenter'                     =>  'DownLoad/View/index',     //课程

        'zhibo'                       =>  'Zhiboke/View/index',     //课程
        'xiaonei'                     =>  'Xiaoneike/View/index',     //课程
        'baolu'                     =>  'Baoluke/View/index',     //课程


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
        'm'                         =>  'Open/View/mindex', // 主页公开课
        'm/open'                    =>  'Open/View/mindex', // 主页公开课
        'm/open/detail'             =>  'Open/View/mdetail', // 公开课详情

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
        'm/message/details'	        =>	'Mobile/Index/messageDetails',//消息详情
        'm/set'	                    =>	'Mobile/Index/set',//个人信息


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