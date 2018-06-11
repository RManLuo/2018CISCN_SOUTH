<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//
//];

return [
    '/'           => 'index/index/index',
    //用户模块
    '/login'       => 'index/user/login',
    '/register'    => 'index/user/register',
    '/logout'      => 'index/user/logout',
    '/user'        => 'index/user/info', //用户详情
    '/pass/reset'  => 'index/user/reset_password',
    '/user/change' => 'index/user/change_password',
    '/mail/change' => 'index/user/change_email',
    '/captcha'     => 'index/user/captcha',
    //购物模块
    '/shop'       => 'index/shop/lst',
    '/info/:id'   => ['index/shop/info', ['id'=> '\d+']],
    '/shopcar'    => 'index/shop/list_shopcar',
    '/shopcar/add'=> 'index/shop/add_shopcar',
    '/pay'        => 'index/shop/pay',
    '/seckill'    => 'index/shop/seckill',
    '/thirdpay'   => 'index/shop/third_pay',
    '/getflag'    => 'index/shop/getflag',
];