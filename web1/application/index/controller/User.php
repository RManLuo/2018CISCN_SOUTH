
<?php
namespace app\index\controller;
use think\Controller;

class User extends Base
{
    public function login()
    {
		if(request() -> isPost()) {
			$callback = $this -> doLogin();
			if(isset($callback['dangerInfo'])) {
				$assign['danger'] = $callback['dangerInfo'];
			} else if(isset($callback['redirect_url'])) {
				$this -> redirect($callback['redirect_url']);
			}
		}
		$captchaData = controller('Captcha') -> getCaptcha();
		$assign['captcha'] = $captchaData;
		$this -> assign($assign);
		return view('User/login');
    }
	public function register()
	{
		if(request() -> isPost()) {
			$callback = $this -> doRegister();
			if(isset($callback['dangerInfo'])) {
				$assign['danger'] = $callback['dangerInfo'];
			} else if(isset($callback['redirect_url'])) {
				$this -> redirect($callback['redirect_url']);
			}
		}
		$captchaData = controller('Captcha') -> getCaptcha();
		$assign['captcha'] = $captchaData;
		$this -> assign($assign);
		return view('User/register');
	}
	public function logout()
	{
		session('user_id', NULL);
		return controller('index/Index') -> index();
	}
	public function pay()
	{
		$data = input('post.');
		if(TRUE !== xsrf_validate($data['_xsrf'])) {
			$assign['danger'] = '请勿非法提交！';
		} else {
			$price = $data['price'];
			if(model('User') -> getIntegralByUserId(session('user_id')) < $price) {
				$assign['danger'] = '剩余积分不足！';
			} else {
				if(FALSE !== model('User') -> decIntegralByUserId(session('user_id'), $price)) {
					$assign['success'] = TRUE;
				} else {
					$assign['danger'] = '购买失败，请稍后重试！';
				}
			}
		}
		$this -> assign($assign);
		return view('User/pay');
	}
	public function changePassword()
	{
		if(request() -> isPost()) {
			$callback = $this -> doChangePassword();
			if(isset($callback['dangerInfo'])) {
				$assign['danger'] = $callback['dangerInfo'];
			} else if(isset($callback['success'])) {
				$assign['success'] = TRUE;
			}
		}
		if(isset($assign)) {
			$this -> assign($assign);
		}
		return view('User/changePassword');
	}
	public function resetPassword()
	{
		if(request() -> isPost()) {
			$callback = $this -> doLogin();
			if(isset($callback['dangerInfo'])) {
				$assign['danger'] = $callback['dangerInfo'];
			} else if(isset($callback['redirect_url'])) {
				$this -> redirect($callback['redirect_url']);
			}
		}
		$captchaData = controller('Captcha') -> getCaptcha();
		$assign['captcha'] = $captchaData;
		$this -> assign($assign);
			return view('User/resetPassword');
	}
	protected function doResetPassword()
	{
		$data = input('post.');
		if(TRUE !== xsrf_validate($data['_xsrf'])) {
			$res['dangerInfo'] = '请勿非法提交！';
			return $res;
		}
		$res['success'] = TRUE;
		return $res;
	}
	protected function doChangePassword()
	{
		$data = input('post.');
		if(TRUE !== xsrf_validate($data['_xsrf'])) {
			$res['dangerInfo'] = '请勿非法提交！';
			return $res;
		}
		if(TRUE !== model('User') -> checkOldPassword(session('user_id'), $data['old_password'])) {
			$res['dangerInfo'] = '原密码错误！';
			return $res;
		}
		if($data['password'] !== $data['password_confirm']) {
			$res['dangerInfo'] = '重复密码不一致！';
			return $res;
		}
		if(FALSE !== model('User') -> changePassword(session('user_id'), $data['password'])) {
			$res['success'] = TRUE;
			return $res;
		}
		$res['dangerInfo'] = '更改失败，请重试！';
		return $res;
	}
	protected function doLogin()
	{
		$data = input('post.');
		if(TRUE !== xsrf_validate($data['_xsrf'])) {
			$res['dangerInfo'] = '请勿非法提交！';
			return $res;
		}
		if(TRUE !== controller('Captcha') -> validateCaptcha($data['captcha_x'], $data['captcha_y'])) {
			$res['dangerInfo'] = '验证码校验失败！';
			return $res;
		}
		if(FALSE !== ($rdata = model('User') -> doLogin($data['username'], $data['password']))) {
			if((FALSE === model('User') -> getUserById($rdata[0]['id'])) || (FALSE === model('User') -> getUserByUsername($data['username']))) {
				dump($rdata);
				exit();
			}
			session('user_id', $rdata[0]['id']);	
			$res['redirect_url'] = url('index/User/index');
			return $res;
		}
		$res['dangerInfo'] = '登录失败，请重试！';
		return $res;
	}
	
	protected function doRegister()
	{
		$data = input('post.');
		if(TRUE !== xsrf_validate($data['_xsrf'])) {
			$res['dangerInfo'] = '请勿非法提交！';
			return $res;
		}
		if(TRUE !== controller('Captcha') -> validateCaptcha($data['captcha_x'], $data['captcha_y'])) {
			$res['dangerInfo'] = '验证码校验失败！';
			return $res;
		}
		if($data['password'] !== $data['password_confirm']) {
			$res['dangerInfo'] = '重复密码不一致！';
			return $res;
		}
		if(FALSE !== ($rdata = model('User') -> doRegister($data['username'], $data['password'], $data['mail']))) {
			if(!empty($data['invite_user'])) {
				model('User') -> incIntegralByUsername($data['invite_user'], 200);
			}
			$res['redirect_url'] = url('index/User/login');
			return $res;
		}
		$res['dangerInfo'] = '注册失败，请重试！';
		return $res;
	}
	
	public function index()
	{
		$userId = session('user_id');
		$data = model('User') -> getUserById($userId);
		$assign['user']['username'] = $data['username'];
		$assign['user']['mail'] = $data['mail'];
		$assign['user']['integral'] = $data['integral'];
		$this -> assign($assign);
		return view('User/index');
	}
}
