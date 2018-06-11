<?php
/**
 * Created by PhpStorm.
 * User: seaii
 * Date: 18-5-21
 * Time: 上午9:32
 */

namespace app\index\model;

use think\Model;
use app\index\model\User;

class Shop extends Model {
    protected $table = 'commoditys';

    public function get_commodity_count() {
        return Shop::count();
    }

    public function get_commoditys_by_limit($start, $limit) {
        return Shop::field('id,name,price')
            ->where('amount', '>', 0)
            ->order('price desc')
            ->limit($start, $limit)->select();
    }

    public function get_commodity_by_id($id) {
        return Shop::where('id', $id)->find();
    }

    public function check_commodity($commodity_id, $price = null) {
        $commodity = Shop::get($commodity_id);
        if($price === null)
            return $commodity && intval($commodity->amount > 0);
        else
            return $commodity && floatval($commodity->price) === $price && intval($commodity->amount) > 0;
    }

    public function pay($user_id, $commodity_id, $price) {
        $user = User::get($user_id);
        if($user->integral < $price)
            return false;
        $user->integral -= $price;
        $user->save();

        $commodity = Shop::get($commodity_id);
        if($commodity->amount < 0)
            return false;
        $commodity->amount -= 1;
        $commodity->save();
        return true;
    }
}