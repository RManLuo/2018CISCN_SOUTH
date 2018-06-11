<?php
/**
 * Created by PhpStorm.
 * User: seaii
 * Date: 18-5-19
 * Time: 下午8:24
 */
namespace app\index\controller;

use think\Controller;
use think\Request;
use app\index\model\Shop as ShopModel;

class Shop extends Controller {
    protected $shop_model;
    protected $shop_validate;
    protected $redis;

    public function __construct(Request $request = null, ShopModel $shop_model) {
        parent::__construct($request);
        $this->shop_model = $shop_model;
        $this->shop_validate = validate('Shop');

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->redis->auth('c4542c9960e4b999c3293a54a7954a14');
    }

    public function lst() {
        $page = input('page');
        $page = intval($page) ? intval($page) : 1;
        $limit = 10;
        $start = $limit * ($page - 1);
        $commoditys = $this->shop_model->get_commoditys_by_limit($start, $limit);
        $this->assign([
            'commoditys'=> $commoditys,
            'preview'=> $page - 1,
            'next'=> $page + 1,
            'limit'=> $limit,
        ]);
        return view();
    }

    public function info($id) {
        $id = intval($id);
        $commodity = $this->shop_model->get_commodity_by_id($id);
        $this->assign([
            'commodity'=> $commodity,
            'thirdpay_url'=> $this->gen_pay_url($id, $commodity->price)['callback_url'],
        ]);
        return view();
    }

    public function list_shopcar(Request $request) {
        if(!session('username'))
            return $this->redirect('/login'); //未登录
        if($request->isPost()) {
            $data = input('post.');
            $commodity_id = session('commodity');
            $user_id = session('id');
            $price = floatval($data['price']);

            $check_result = $this->shop_validate->scene('pay_shopcar')->check($data);
            $check_commodity = $this->shop_model->check_commodity($commodity_id, $price);
            if(!$check_result || !$check_commodity) {
                $this->assign('danger', 1);
                return view();
            }

            $result = $this->shop_model->pay($user_id, $commodity_id, $price);
            if(!$result) {
                $this->assign('danger', 1);
                return view();
            } else {
                $this->assign('success', 1);
                return view();
            }
        }

        $commodity_id = intval(session('commodity'));
        $commodity = $this->shop_model->where('id', $commodity_id)->field('name,price')->find();
        $this->assign('commodity', $commodity);
        return view();
    }

    public function add_shopcar(Request $request) {
        if(!session('username'))
            return $this->redirect('/login'); //未登录

        if($request->isPost()) {
            $data = input('post.');
            $commodity_id = intval($data['id']);

            $check_result = $this->shop_validate->scene('add_shopcar')->check($data);
            $check_commodity = $this->shop_model->check_commodity($commodity_id);
            if(!$check_result || !$check_commodity) {
                return var_dump($check_commodity);
                return $this->redirect('/shopcar');
            }

            session('commodity', $commodity_id);
            return $this->redirect('/shopcar');
        }
    }

    public function pay(Request $request) {
        if(!session('username'))
            return $this->redirect('/login'); //未登录

        if($request->isPost()) {
            $data = input('post.');
            $commodity_id = intval($data['id']);
            $user_id = session('id');
            $price = floatval($data['price']);

            $check_result = $this->shop_validate->scene('pay')->check($data);
            $check_commodity = $this->shop_model->check_commodity($commodity_id, $price);
            if(!$check_result || !$check_commodity) {
                $this->assign('danger', 1);
                return view();
            }

            $result = $this->shop_model->pay($user_id, $commodity_id, $price);
            if(!$result) {
                $this->assign('danger', 1);
                return view();
            } else {
                $this->assign('success', 1);
                return view();
            }
        }
    }

    public function third_pay(Request $request) {
        if(!session('username'))
            return $this->redirect('/login'); //未登录

        $commodity_id = intval(input('commodity_id'));
        $price = floatval(input('price'));
        $sign = input('sign');
        $real_sign = $this->gen_pay_url($commodity_id, $price)['sign'];
        if($sign !== $real_sign) {
            $this->assign('danger', 1);
            return view();
        }

        $user_id = session('id');
        $result = $this->shop_model->pay($user_id, $commodity_id, $price);
        if(!$result) {
            $this->assign('danger', 1);
            return view();
        } else {
            $commodity = $this->shop_model->get($commodity_id);
            if($commodity->name === 'ticket')
                session('admin', 1);
            $this->assign('success', 1);
            return view();
        }
    }

    private function gen_pay_url($id, $price) {
        $parm = array(
            //'uid'=>'1',
            'commodity_id'=>$id,
            'price'=>$price,
        );
        ksort($parm);
        $pay_key = db('pay_config')->where('id','1')->find()['pay_key'];
        $mark = http_build_query($parm);
        $parm['sign'] = md5($mark.$pay_key);
        $callback_url = '/thirdpay?'.http_build_query($parm);
        $result = array(
            'sign'=> $parm['sign'],
            'callback_url'=> $callback_url,
        );
        return $result;
    }

    public function seckill(Request $request) {
        if($request->isPost()) {
            $data = input('post.');
            $id = intval($data['id']);
            $check_result = $this->shop_validate->scene('seckill')->check($data);
            if(!$check_result) {
                //return $this->shop_validate->getError();
                $this->assign('danger', 1);
                return view();
            }

            $this->gen_queue($id);
            $count = $this->redis->lPop('commoditys');
            if(!$count) {
                $this->assign('danger', 1);
                return view();
            }

            $commodity = $this->shop_model->get($id);
            $commodity->amount -= 1;
            $commodity->save();
            $this->assign('success', 1);
            return view();
        }
        return view();
    }

    private function gen_queue($commodity_id) {
        if(!$this->redis->exists('commoditys')) {
            $commodity = $this->shop_model->get($commodity_id);
            if($commodity) {
                $store = $commodity->amount;
                for($i = 0; $i < $store; $i++)
                    $this->redis->lPush('commoditys', 1);
            }
        }
    }

    public function getflag() {
        if(session('admin') === 1){
            if(isset($_POST['d'])){
                $flag = unserialize($_POST['d']);
            }else{
                $flag = new Flag;
            }
            echo $flag->getflag();
        }else{
            echo 'deny';
        }

    }
}

class Flag{
    public $flag;
    public static function flag($r){
        return file_get_contents($r->flag);
    }
    public function getflag(){
        $this->flag='/flag';
        return self::flag($this);
    }
}