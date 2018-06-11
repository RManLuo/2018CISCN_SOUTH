
<?php
namespace app\index\controller;
use think\Controller;

class Shopcar extends Base
{
	public function index()
	{
		if(request() -> isPost()) {
			$callback = $this -> doPay();
			if(isset($callback['dangerInfo'])) {
				$assign['danger'] = $callback['dangerInfo'];
			} else if(NULL !== $callback['success']) {
				$assign['success'] = TRUE;
			}
		}
		if(session('?commodity_id')) {
			$assign['data'] = model('Shop') -> getCommodityById(session('commodity_id'));
		}
		if(isset($assign)) {
			$this -> assign($assign);
		}
		return view('Shopcar/index');
	}
	
	public function addCommodity()
	{
		$data = input('post.');
		if(TRUE !== xsrf_validate($data['_xsrf'])) {
			$assign['danger'] = '请勿非法提交！';
			$this -> assign($assign);
			return view('Shopcar/index');
		}
		session('commodity_id', $data['id']);
		$this -> redirect('index/Shopcar/index');
		
	}
	
	public function doPay()
	{
		$data = input('post.');
		if(TRUE !== xsrf_validate($data['_xsrf'])) {
			$res['dangerInfo'] = '请勿非法提交！';
			return $res;
		}
		$price = $data['price'];
		if(model('User') -> getIntegralByUserId(session('user_id')) < $price) {
			$res['dangerInfo'] = '剩余积分不足！';
			return $res;
		} 
		if(FALSE !== model('User') -> decIntegralByUserId(session('user_id'), $price)) {
			$res['success'] = TRUE;
			session('commodity_id', NULL);
			return $res;
		}
		$res['dangerInfo'] = '购买失败，请稍后重试！';
		return $res;
	
	}
}