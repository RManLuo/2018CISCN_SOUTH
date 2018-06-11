
<?php
namespace app\index\controller;
use think\Controller;

class Shop extends Base
{
    public function index()
    {
		$res = model('Shop') -> getCommoditys();
		$assign['data'] = $res;
		$assign['pager'] = $res -> render();
		$this -> assign($assign);
		return view('Shop/index');
    }
	public function info()
	{
		$id = input('id');
		if(FALSE === ($data = model('Shop') -> getCommodityById($id))) {
			$this -> redirect('index/Shop/index');
		}
		$assign['data'] = $data;
		$this -> assign($assign);
		return view('Shop/info');
	}
	public function secKill()
	{
		if(request() -> isPost()) {
			$callback = $this -> doSecKill();
			if(isset($callback['dangerInfo'])) {
				$assign['danger'] = $callback['dangerInfo'];
			} else if(isset($callback['success'])) {
				$assign['success'] = TRUE;
			}
		}
		if(isset($assign)) {
			$this -> assign($assign);
		}
		return view('Shop/secKill');
	}
	public function doSecKill()
	{
		$data = input('post.');
		if(TRUE !== xsrf_validate($data['_xsrf'])) {
			$res['dangerInfo'] = '请勿非法提交！';
			return $res;
		}
		if(FALSE !== model('index/Shop') -> decAmountById($data['id'])) {
			$res['success'] = TRUE;
			return $res;
		}
	}
	private function generate()
	{
		for($i = 0; $i < 100; $i++) {
			$data['name'] = create_random_str(16);
			$data['desc'] = create_random_str(100);
			$data['price'] = rand(10, 200);
			db('commoditys') -> insert($data);
		}
	}
}
