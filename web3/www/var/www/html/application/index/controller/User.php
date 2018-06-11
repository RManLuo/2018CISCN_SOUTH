<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use app\index\model\User as UserModel;

class User extends Controller {
    protected $user_validate;
    protected $user_model;

    public function __construct(Request $request = null, UserModel $user_model)
    {
        parent::__construct($request);
        $this->user_validate = validate('User');
        $this->user_model = $user_model;
    }

    public function login(Request $request) {
        if($request->isPost()) {
            $username = input('username');
            $password = input('password');

            if(!$this->check_captcha()) {
                $this->assign($this->get_captcha_info());
                //var_dump($this->get_captcha_info());
                return view();
            }

            $data = array('username'=> $username, 'password'=> $password);
            $check_result = $this->user_validate->scene('login')->check($data);
            if(!$check_result) {
                $this->assign('danger', 1);
                $this->assign($this->get_captcha_info());
                return view();
            }

            $user = $this->user_model->check_login($username, $password);
            if(!$user) {
                $this->assign('danger', 1);
                $this->assign($this->get_captcha_info());
                return view();
            } else {
                session('id', $user->id);
                session('username', $user->username);
                return $this->redirect('/shop');
            }
        }
        gen_captcha();
        $this->assign($this->get_captcha_info());
        return view();
    }

    public function logout() {
        session(null);
        $this->redirect('/login');
        return;
    }

    public function register(Request $request) {
        if($request->isPost()) {
            $data = $this->safe_check(input('post.'));

            if(!$this->check_captcha()) {
                $this->assign($this->get_captcha_info());
                return view();
            }

            //检查数据合法性
            $check_result = $this->user_validate->scene('register')->check($data);
            if(!$check_result || $data['password'] !== $data['password_confirm']) {
                $this->assign('danger', 1);
                $this->assign($this->get_captcha_info());
                return view();
                //return $this->user_validate->getError();
            }
            $this->user_model->add_user($data);

            if($data['invite_user']) {
                //一个用户最多邀请10个人
                if(!$this->user_model->check_invite_user($data['invite_user'])) {
                    $this->assign('danger', 1);
                    $this->assign($this->get_captcha_info());
                    return view();
                }
                $this->user_model->add_integral($data['invite_user']);
            }

            return $this->redirect('/login');
        }
        gen_captcha();
        $this->assign($this->get_captcha_info());
        return view();
    }

    public function change_password(Request $request) {
        if(!session('username'))
            return $this->redirect('/login'); //未登录
        if($request->isPost()) {
            $data = $this->safe_check(input('post.'));
            $id = session('id');

            $check_result = $this->user_validate->scene('change_pass')->check($data);
            $check_pass_result = $this->user_model->check_old_password($id, $data['old_password']); //检查旧密码
            if(!$check_result || !$check_pass_result || $data['password'] !== $data['password_confirm']) {
                //return var_dump($check_pass_result);
                $this->assign('danger', 1);
                $this->assign($this->get_captcha_info());
                return view();
            }

            $result = $this->user_model->change_password($id, $data['password']);
            if(!$result) {
                $this->assign('danger', 1);
                $this->assign($this->get_captcha_info());
                return view();
            }

            session(null); //修改密码后清空session
            gen_captcha();
            $this->assign('success', 1);
            $this->assign($this->get_captcha_info());
            return view();
        }
        return view();
    }

    public function reset_password(Request $request) {
        if($request->isPost()) {
            $data = input('post.');
            if(!$this->check_captcha()) {
                $this->assign($this->get_captcha_info());
                return view();
            }

            $check_result = $this->user_validate->scene('reset_pass')->check($data);
            if(!$check_result) {
                $this->assign('danger', 1);
                return view();
            }


            return $this->redirect('/login');
        }

        gen_captcha();
        $this->assign($this->get_captcha_info());
        return view();
    }

    public function info() {
        if(!session('username'))
            return $this->redirect('/login'); //未登录
        $id = session('id');
        $user = $this->user_model->where('id', $id)->find();
        $this->assign('user', $user);
        return view();
    }

    public function change_email(Request $request) {
        if(!session('username'))
            return $this->redirect('/login'); //未登录

        if($request->isPost()) {
            $email = input('post.mail/a');
            //$this->user_validate->scene('change_mail')->check(input('post.'));
            $user_id = session('id');
            $this->user_model->where('id', $user_id)->update(['mail'=> $email]);
            $this->assign('success', 1);
            return view();
        }
        return view();
    }

    public function captcha() {
        $uuid = $this->get_captcha_info()['uuid'];
        $jpg_path = __DIR__.'/../captcha/jpgs';
        $filename = $jpg_path.'/ques'.$uuid.'.jpg';
        $img = file_get_contents($filename);
        return response($img, 200)->contentType('image/jpg');
    }

    private function get_captcha_info() {
        $info = json_decode(file_get_contents(__DIR__.'/../../../runtime/captcha.temp'));
        $captcha_info = array(
            'uuid'=> $info->uuid,
            'question'=> $info->question,
        );
        return $captcha_info;
    }

    private function check_captcha() {
        $x = input('captcha_x');
        $y = input('captcha_y');
        if (isset($x) && isset($y)) {
            $uuid = $this->get_captcha_info()['uuid'];
            $answer = get_answer($uuid);

            if (floatval($answer['ans_pos_x_1']) <= $x && $x <= (floatval($answer['ans_pos_x_1']) + floatval($answer['ans_width_x_1'])))
                if (floatval($answer['ans_pos_y_1']) <= $y && $y <= (floatval($answer['ans_pos_y_1']) + floatval($answer['ans_height_y_1'])))
                    return true;
        }
        return false;
    }

    private function safe_check($info) {
        if(is_array($info)) {
            foreach ($info as $key=> $value) {
                $info[$key] = $this->safe_check($value);
            }
        } elseif(is_string($info)) {
            $info = htmlspecialchars($info);
        }
        return $info;
    }
}