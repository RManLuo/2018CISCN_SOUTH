<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
	'login' => 'index/User/login',
	'logout' => 'index/User/logout',
	'register' => 'index/User/register',
	'pay' => 'index/User/pay',
	'captcha' => 'index/Captcha/showCaptchaImage',
	'shop' => 'index/Shop/index',
	'user' => 'index/User/index',
	'user/change' => 'index/User/changePassword',
	'pass/reset' => 'index/User/resetPassword',
	'info/:id' => 'index/Shop/info',
	'shopcar' => 'index/Shopcar/index',
	'shopcar/add' => 'index/Shopcar/addCommodity',
	'seckill' => 'index/Shop/seckill',
    'favicon' => 'index/Index/favicon'

];