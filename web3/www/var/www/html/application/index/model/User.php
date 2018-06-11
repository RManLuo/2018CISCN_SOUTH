<?php
/**
 * Created by PhpStorm.
 * User: seaii
 * Date: 18-5-19
 * Time: 下午10:42
 */
namespace app\index\model;

use think\Model;

class User extends Model {
    public function add_user($data) {
        $user = new User();
        $user->username = $data['username'];
        $salt = $this->gen_salt();
        $user->password = $this->hash($salt, $data['password']);
        $user->salt = $salt;
        $user->mail = $data['mail'];
        $user->integral = 1000.1;
        $user->recommend = 0;
        $user->save();
    }

    public function check_invite_user($invite_user) {
        $invite_user = User::getByUsername($invite_user);
        if(!$invite_user) //用户不存在
            return false;
        if($invite_user->recommend >= 10) //邀请数大于10
            return false;
        return true;
    }

    public function check_login($username, $password) {
        $user = User::getByUsername($username);
        if(!$user)
            return false; //用户不存在
        if($this->hash($user->salt, $password) === $user->password)
            return $user; //登录成功
        else
            return false; //密码错误
    }

    public function add_integral($invite_user) {
        $invite_user = User::getByUsername($invite_user);
        $invite_user->integral += 10; //邀请一个用户加10积分
        $invite_user->recommend += 1;
        $invite_user->save();
    }

    public function check_old_password($user_id, $old_password) {
        $user = User::get($user_id);
        if(!$user)
            return false;
        if($user->password !== $this->hash($user->salt, $old_password))
            return false;
        return true;
    }

    public function change_password($user_id, $new_password) {
        $user = User::get($user_id);
        if(!$user)
            return false;

        $salt = $this->gen_salt();
        $user->password = $this->hash($salt, $new_password);
        $user->salt = $salt;
        $user->save();
        return true;
    }

    private function gen_salt() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $salt = '';
        for ($i = 0; $i < 6; $i++) {
            $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $salt;
    }

    private function hash($salt, $password) {
        return md5(sha1($salt.$password));
    }
}