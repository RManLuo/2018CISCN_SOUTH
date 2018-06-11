<?php
/**
 * Created by PhpStorm.
 * User: seaii
 * Date: 18-5-19
 * Time: 下午10:28
 */
namespace app\index\validate;

use think\Validate;

class Shop extends Validate {
    protected $rule = [
        'id'=> 'require|token:_xsrf',
        'price'=> 'require|token:_xsrf',
    ];

    protected $scene = [
        'pay' => ['id', 'price.require'],
        'add_shopcar'=> ['id'],
        'pay_shopcar'=> ['price'],
        'seckill'=> ['id'],
    ];
}