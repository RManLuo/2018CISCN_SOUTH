
<?php
namespace app\index\model;
use think\Model;

class Shop extends Model {
	
	protected $table = 'commoditys';
	
	public function getCommoditys() 
	{
		return $this -> paginate(20);
	}
	
	public function getCommodityById($id)
	{
		$condition['id'] = $id;
		if(($res = $this -> where($condition) -> find()) !== FALSE) {
			return $res;
		}
		return FALSE;
	}
	
	public function decAmountById($CommodityId)
	{
		$condition['id'] = $CommodityId;
		return $this -> where($condition) -> setDec('amount', 1);
	}
	
}