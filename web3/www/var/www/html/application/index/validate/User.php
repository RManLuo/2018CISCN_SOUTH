<?php
/**
 * Created by PhpStorm.
 * User: seaii
 * Date: 18-5-19
 * Time: 下午10:28
 */
namespace app\index\validate;

use think\Validate;

class User extends Validate {
    protected $rule = [
        'username'=> 'unique:user|require|max:25|token:_xsrf',
        'password'=> 'require|min:6',
        'password_confirm'=> 'require|token:_xsrf',
        'old_password'=> 'require',
        'mail'=> 'require|token:_xsrf',
    ];

    protected $scene = [
        'login'      => ['username.require, username.max', 'username.token', 'password'],
        'register'   => ['username', 'password.require', 'mail.require', 'password_confirm.require', ],
        'change_pass'=> ['password', 'old_password', 'password_confirm', ],
        'reset_pass' => ['mail',],
    ];
}