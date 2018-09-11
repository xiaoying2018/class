<?php

namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Model\StudentModel;
use http\Env\Response;
use Think\Controller;
use Think\Exception;

class AuthController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		if(ACTION_NAME == 'logout') return ;

	}

	public function login_view (){
		$this->display();
	}

	public function login ()
	{
		try{
			$this->isLoginYet() && E('您已经登录了');
            // TODO change
			if( IS_POST && IS_AJAX ){
				$params         =   I('post.');
                // 参数检验
				$this->checkLoginParams( $params ) || E('用户名或密码不合法!',203);
                // 证书验证
				$studentModel       =   new StudentModel();
				$params             =   array_map( 'trim', $params );
				( $info=$studentModel->student_login( $params['username'], $params['password'] ) ) || E('用户名或密码不合法!',203);
                //
				session('_student', $info);
                //
				$result             =   [
					'result'            =>  true,
					'msg'               =>  '登录成功!',
				];
				$this->response( $result, '/' );
			}
			E('非法操作',200);
		}catch (Exception $e){
			$return = [
				'result'        =>  false,
				'msg'           =>  $e->getMessage(),
			];
			$url            =   $e->getCode() == 203 ? U('Auth/login_view') : null;

			$this->response($return,$url);
		}
	}

	public function logout ()
	{
		$result = [
			'result'    =>  false,
			'msg'       =>  '你还没有登录',
		];
		$this->isLoginYet() || $this->response( $result, '/' );

		session(null);
		$result         =   [
			'result'        =>  true,
			'msg'           =>  '退出登录成功',
		];
		$this->response($result, U('Auth/login_view'));
	}

	public function reset() {
		$changeway = I('post.changeway');
		if ($changeway == 1) {
			try {
				if (IS_POST && IS_AJAX) {
					$params = I('post.');
					$model = new StudentModel();
					$this->checkResetParams($params) || E('参数有误');
					$model->student_login(session('_student.mobile'), $params['oldpasswd']) || E('旧密码不正确');
					$data = [
						'id' => (int) session('_student.id'),
						'password' => password_hash(trim($params['newpasswd']), PASSWORD_BCRYPT)
					];
					
					$model->field('password')->save($data) === false && E($model->getDbError());
					
					$this->ajaxReturn(['result' => true]);
				}
				E('非法操作');
			} catch (Exception $e) {
				$result = [
					'result' => false,
					'error' => $e->getMessage(),
				];
				$this->ajaxReturn($result);
			}
		} else if ($changeway == 2) {
			try {
				if (IS_POST && IS_AJAX) {
					$res = $this->loginbytels();
					if ($res != 'success') {
						$this->ajaxReturn(['status' => false, 'message' => $res['message']]);
					}
					$params = I('post.');
					$model = new StudentModel();
					$data = [
						'id' => (int) session('_student.id'),
						'password' => password_hash(trim($params['newpasswd']), PASSWORD_BCRYPT)
					];
					
					$model->field('password')->save($data) === false && E($model->getDbError());
					
					$this->ajaxReturn(['status' => true,'message'=>'修改成功']);
				}
				E('非法操作');
			} catch (Exception $e) {
				$result = [
					'result' => false,
					'error' => $e->getMessage(),
				];
				$this->ajaxReturn($result);
			}
		} else {
			$result = [
				'result' => false,
				'error' => '参数不正确',
			];
			$this->ajaxReturn($result);
		}
	}

	public function isLoginYet ()
	{
		return session('?_student');
	}

	protected function checkLoginParams ($params)
	{
		return exists_key( 'username', $params )
		&& exists_key( 'password', $params )
		&& $params['username']
		&& $params['password'];
	}

	protected function checkResetParams ($params)
	{
		return exists_key( 'oldpasswd', $params )
		&& exists_key( 'newpasswd', $params )
		&& exists_key( 'newpasswd2', $params )
		&& $params['oldpasswd']
		&& $params['newpasswd']
		&& $params['newpasswd2']
		&& ($params['newpasswd']===$params['newpasswd2']);
	}

	protected function checkResetParamss($params) {
		return exists_key('newpasswd', $params) && exists_key('newpasswd2', $params) && $params['newpasswd'] && $params['newpasswd2'] && ($params['newpasswd'] === $params['newpasswd2']);
	}

    // 发送短信验证码
	public function sendsms() {

		try {

			$mobile = I('post.tel');

			if ($mobile == '') {
				throw new Exception('手机号不能为空', 208);
			}
			
            //过期时间，分钟
			$overtime = 5;
            // 验证码是否存在且合法
			if ($this->checkVerifyExistsYets($mobile))
				throw new Exception('验证码已发送过', 208);

			$code = rand(1000, 9999);
			
			$e = 1200;
			$expr = ceil($e / 60);
			$content = $msg = "【小莺出国】您的四位数字登录验证码为：{$code} ;有效期为{$expr}分钟";

			$url = "http://api.smsbao.com/sms?u=everelite&p=".md5('invY1234')."&m={$mobile}&c={$content}";
			$send_res = file_get_contents($url);
			
			$oldCount = 0;
			if ($oldSession = session('mobile_ver')) {
				$oldCount = $oldSession['count'] ?: 0;
			}
			$signData = [
				'mobile' => $mobile,
				'code' => $code,
				'exptime' => time() + $e,
				'count' => ++$oldCount,
			];
			session('mobile_ver', $signData);
			
//            return session('mobile_ver');
            // 刷新token
			$result = [
				'status' => true,
				'message' => '发送成功',
//                'code'=>$code
//                '_token' => [
//                    'sign' => $this->_token,
//                    'value' => $this->_refresh_token('_s'),
//                ],
			];
            // 成功返回
			$this->ajaxReturn($result);
		} catch (Exception $e) {
			$this->ajaxReturn(['status' => false,  'message' => $e->getMessage()]);
		}
	}

    // 短信登录
	public function smslogin() {
		
		try {
			$res = $this->loginbytel();

			if($res != 'success')
			{
				$this->ajaxReturn(['status' => false,  'message' => $res['message']]);
			}

			$this->isLoginYet() && E('您已经登录了');
            // TODO change
			if (IS_POST && IS_AJAX) {
				$params = I('post.');

				$studentModel = new StudentModel();

				$info = $studentModel->field('id,code,realname,mobile,email,password')->where(['mobile'=>['eq',$params['tel']]])->find();

				if (!$info)
				{
					$this->ajaxReturn(['status' => false,  'message' => '该账号不存在,赶快去注册吧']);
				}

                //
				session('_student', $info);
                //
				$result = [
					'status' => true,
					'message' => '登录成功!',
				];
				$this->response($result, '/');
			}
			E('非法操作', 200);
		} catch (Exception $e) {
			$return = [
				'status' => false,
				'message' => $e->getMessage(),
			];
			$url = $e->getCode() == 203 ? U('Auth/login_view') : null;

			$this->response($return, $url);
		}
	}

    // check 验证码 (短信登录)
	public function loginbytel() {

		$verify_key = 'mescode';
		$data = I('post.');

		$origin_code = $data[$verify_key];
		$server_code = session('mobile_ver');
        // 服务端验证码是否存在
		if (is_null($server_code))
		{
			return ['message' => '验证码不正确', 'status' => true];
		}
        // 当前手机是否是接收验证码手机
		if ($server_code['mobile'] != $data['tel'])
		{
			return ['message' => '验证码不正确','status' => true];
		}
        // 验证码是否过期
		if ($server_code['exptime'] < time()) {
            session('mobile_ver', null);// 销毁旧验证码
            return ['message' => '验证码已过期,请重新获取','status' => true];
        }
        // 验证码不正确
        if ($server_code['code'] != $origin_code)
        {
        	return ['message' => '验证码错误', 'status' => true];
        }
        // 销毁数据中的验证码
        unset($data[$verify_key]);

        return 'success';

    }

    // check 验证码 (短信登录)
    public function loginbytels() {

    	$verify_key = 'mescode';
    	$data = I('post.');

    	$origin_code = $data[$verify_key];
    	$server_code = session('mobile_ver');
        // 服务端验证码是否存在
    	if (is_null($server_code))
    	{
    		return ['message' => '验证码不正确', 'status' => true];
    	}
        // 当前手机是否是接收验证码手机
    	if ($server_code['mobile'] != $data['newtel'])
    	{
    		return ['message' => '验证码不正确','status' => true];
    	}
        // 验证码是否过期
    	if ($server_code['exptime'] < time()) {
            session('mobile_ver', null);// 销毁旧验证码
            return ['message' => '验证码已过期,请重新获取','status' => true];
        }
        // 验证码不正确
        if ($server_code['code'] != $origin_code)
        {
        	return ['message' => '验证码错误', 'status' => true];
        }
        // 销毁数据中的验证码
        unset($data[$verify_key]);

        return 'success';

    }

    protected function checkVerifyExistsYets($mobile) {
    	$verifyCode = session('mobile_ver');
    	if ($verifyCode['mobile'] == $mobile && (int) $verifyCode['exptime'] > time()) {
    		if ($verifyCode['count'] >= 7) {
    			throw new Exception('短信发送过于频繁，请稍后再试', 208);
    		}
    		return true;
    	} else {
    		return false;
    	}
    }

    // 发送短信验证码
    public function sendallsms() {

    	try {
    		$pre = I('post.pre');
    		$mobile = I('post.tel');

    		if ($mobile == '') {
    			throw new Exception('手机号不能为空', 208);
    		}
            //过期时间，分钟
    		$overtime = 5;

    		$code = rand(1000, 9999);
    		$e = 1200;
    		$expr = ceil($e / 60);
    		if ($pre == '86') {
    			$content = $msg = "【小莺出国】您的四位数字登陆验证码为：{$code} ;有效期为{$expr}分钟";
    			$url = "http://api.smsbao.com/sms?u=everelite&p=" . md5('invY1234') . "&m={$mobile}&c={$content}";
    			$send_res = file_get_contents($url);
    		} else {
    			import('Vendor.aliyun.api_demo.SmsSend');
    			$intermobile = '00' . $pre . $mobile;
    			$response = \SmsSend::sendSms($intermobile, '小莺出国', 'SMS_138079875', $code);
    		}


    		$oldCount = 0;
    		if ($oldSession = session('mobile_ver')) {
    			$oldCount = $oldSession['count'] ?: 0;
    		}
    		$signData = [
    			'mobile' => $mobile,
    			'code' => $code,
    			'exptime' => time() + $e,
    			'count' => ++$oldCount,
    		];
    		session('mobile_ver', $signData);

//            return session('mobile_ver');
            // 刷新token
    		$result = [
    			'status' => true,
    			'message' => '发送成功',
//                'code'=>$code
//                '_token' => [
//                    'sign' => $this->_token,
//                    'value' => $this->_refresh_token('_s'),
//                ],
    		];
            // 成功返回
    		$this->ajaxReturn($result);
    	} catch (Exception $e) {
    		$this->ajaxReturn(['status' => false, 'message' => $e->getMessage()]);
    	}
    }

    ##修改手机号

    public function changephonebycode() {
    	$res = $this->loginbytels();
    	if ($res != 'success') {
    		$this->ajaxReturn(['status' => false, 'message' => $res['message']]);
    	}
		$mobiledata = M('students')->where(['mobile'=>I('post.newtel')])->find();
		if($mobiledata){
			$this->ajaxReturn(['status'=>false,'message'=>'该手机号已存在！']);
		}
    	if(I('post.pre')==''){
    	    $pre = '86';
        }else{
    	    $pre = I('post.pre');
        }
    	$data = [
    		'mobile' => I('post.newtel'),
            'pre'=>$pre
    	];
    	$data1 = [
    		'bind_mobile' => I('post.newtel')
    	];
    	$saves = M('students')->where(['id' => session('_student.id')])->save($data);
    	$saves_profile =  M('student_profile')->where(['student_id'=>session('_student.id')])->save($data1);
    	if ($saves === false || $saves_profile===false) {
    		$result = [
    			'status' => false,
    			'message' => '修改失败'
    		];
	            // 成功返回
    		$this->ajaxReturn($result);
    	} else {
    		$result = [
    			'status' => true,
    			'message' => '修改成功'
    		];
	            // 成功返回
    		$this->ajaxReturn($result);
    	}
    }

}