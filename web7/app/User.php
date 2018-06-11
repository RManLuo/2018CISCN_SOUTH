<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = "users";
    public $timestamps = false;

    public function check($username, $password) {
        return Hash::check(hash('sha256', $password),$this->where('username', '=', $username)->value('password'));
    }

    public function pay($username, $num) {
        $res = $this->where('username', '=', $username)->value('integral') - $num;
        if($res < 0) {
            return false;
        }
        return $res;
    }
}
