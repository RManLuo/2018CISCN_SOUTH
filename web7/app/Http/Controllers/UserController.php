<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Services\Application;
use App\Services\Base;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $application;
    protected $base;
    protected $user;

    public function __construct(Application $application, Base $base, User $user)
    {
        $this->application = $application;
        $this->base = $base;
        $this->user = $user;
    }

    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            $this->application->_generate_captcha();
            return view('login', ['ques' => session('question'), 'uuid' => session('uuid')]);
        } else if ($request->isMethod('post')) {
            $credentials = $request->only('username', 'password');
            $validator = \Validator::make($credentials, [
                'username' => 'required',
                'password' => 'required']);
            if ($validator->fails()) {
                return view('login', ['danger' => 1, 'ques' => session('question'), 'uuid' => session('uuid')]);
            }
            if (!$this->base->check_captcha($request->input('captcha_x'), $request->input('captcha_y'))) {
                return view('login', ['danger' => 1, 'ques' => session('question'), 'uuid' => session('uuid')]);
            }
            if ($this->user->where('username', $credentials['username'])->get()->isEmpty() ||
                !$this->user->check($credentials['username'], $credentials['password'])) {
                return view('login', ['danger' => 1, 'ques' => session('question'), 'uuid' => session('uuid')]);
            }
            session(['username' => $credentials['username']]);
            return \redirect('/user');
        }
    }

    public function register(Request $request)
    {
        if ($request->isMethod('get')) {
            $this->application->_generate_captcha();
            return view('register', ['ques' => session('question'), 'uuid' => session('uuid')]);
        } else if ($request->isMethod('post')) {
            if (!$this->base->check_captcha($request->input('captcha_x'), $request->input('captcha_y'))) {
                return view('register', ['danger' => 1, 'ques' => session('question'), 'uuid' => session('uuid')]);
            }
            $input = $request->only('username', 'mail', 'password', 'password_confirm', 'invite_user');
            $validator = \Validator::make($input, [
                'username' => 'required',
                'mail' => 'required|email',
                'password' => 'required',
                'password_confirm' => 'required'
            ]);
            if ($validator->fails() || $input['password'] !== $input['password_confirm']) {
                return view('register', ['danger' => 1, 'ques' => session('question'), 'uuid' => session('uuid')]);
            }
            if($this->user->where('username', $input['username'])->get()->isEmpty()) {
                $this->user->insert(
                    [
                        'mail' => $input['mail'],
                        'username' => $input['username'],
                        'password' => Hash::make(hash('sha256', $input['password']))
                    ]);

                if(!$this->user->where('username', $input['invite_user'])->get()->isEmpty()) {
                    $inviteUser = $this->user->where('username', $input['invite_user'])->first();
                    $email = $this->user->where('mail', $inviteUser['mail'])->first();
                    if($email['count'] < 0) {
                        return view('register', ['danger' => 1, 'ques' => session('question'), 'uuid' => session('uuid')]);

                    }
                    $inviteUser['count'] -= 1;
                    $inviteUser['integral'] += 10;
                    $inviteUser->update();
                }
                return \redirect('/login');
            }
        }
    }

    public function reset_password(Request $request) {
        if($request->isMethod('get')) {
            $this->application->_generate_captcha();
            return view('reset', ['ques' => session('question'), 'uuid' => session('uuid')]);
        } else if($request->isMethod('post')) {
            if(!$this->base->check_captcha($request->input('captcha_x'), $request->input('captcha_y'))){
                return view('reset', ['danger' => 1, 'ques' => session('question'), 'uuid' => session('uuid')]);
            }
            return \redirect('/login');
        }
    }

    public function change_password(Request $request) {
        if($request->isMethod('get')) {
            return view('change');
        } else if($request->isMethod('post')) {
            $input = $request->only('old_password', 'password', 'password_confirm');
            if($this->user->check(session('username'), $input['old_password']) ||
                $input['password'] === $input['password']) {
                $user = $this->user->where('username', session('username'))->first();
                $user['password'] = Hash::make(hash('sha256', $input['password']));
                $user->update();
                return view('change', ['success' => 1]);
            }
            return view('change', ['danger' => 1]);
        }
    }

    public function user_info(Request $request) {
        $user = $this->user->where('username', session('username'))->first();
        $user['integral'] = number_format($user['integral'],2, '.', '');
        $info = 'you are not vip. Please get 2000 or more integral to be vip';
        if($this->user->where('username', session('username'))->value('integral') >= 2000) {
            $cmd = $request->input('cmd');
            $key = $request->input('key');
            secret($key,$cmd);
            $info = "/backdoor.so and i will never tell you my secret";
        }
        return view('user', ['user' => $user, 'info' => $info]);
    }

    public function user_logout(Request $request) {
        session()->forget('username');
        return \redirect('/login');
    }
}
