
<?php
namespace app\index\model;
use think\Model;

class User extends Model {
	
	protected $salt = '4pU8i2';
	
	public function doLogin($username, $password) {
		$username = trim($username);
		$password = generate_hash_with_salt($password, $this -> salt);
		$filter = "union|select|sleep|--";
		$this -> attackFilter($username, $filter);
		$res = db() -> query("SELECT * FROM `user` WHERE `username` = '$username' AND `password` = '$password'");
		if($res) {
			return $res;
		}
		return FALSE;
	}
	
	public function getUserById($id) {
		$condition['id'] = $id;
		$res = $this -> where($condition) -> find();
		if($res) {
			return $res;
		}
		return FALSE;
	}
	
	public function getUserByUsername($username) {
		$condition['username'] = $username;
		$res = $this -> where($condition) -> find();
		if($res) {
			return $res;
		}
		return FALSE;
	}
	
	public function doRegister($username, $password, $mail) {
		if($this -> getUserByUsername($username) !== FALSE) {
			return FALSE;
		}
		$data['username'] = trim($username);
		$data['password'] = generate_hash_with_salt($password, $this -> salt);
		$data['mail'] = $mail;
		if($this -> insert($data) !== FALSE) {
			$res = $this -> getUserByUsername($data['username']);
			return $res['id'];
		}
		return FALSE;
	}
	
	public function getIntegralByUserId($userId) {
		$condition['id'] = $userId;
		$res = $this -> where($condition) -> find();
		return $res['integral'];
	}
	
	public function incIntegralByUsername($username, $integral) {
		$condition['username'] = $username;
		return $this -> where($condition) -> setInc('integral', $integral);
	}
	
	public function decIntegralByUserId($userId, $integral) {
		$condition['id'] = $userId;
		return $this -> where($condition) -> setDec('integral', $integral);
	}
	
	public function checkOldPassword($userId, $old_password) {
		$condition['id'] = $userId;
		$condition['password'] = generate_hash_with_salt($old_password, $this -> salt);
		if($this -> where($condition) -> find()) {
			return TRUE;
		}
		return FALSE;
	}
	
	public function changePassword($userId, $password) {
		$condition['id'] = $userId;
		$data['password'] = generate_hash_with_salt($password, $this -> salt);
		return $this -> where($condition) -> update($data);
	}
	
	private function attackFilter($strValue, $arrReq){
      
		if (is_array($strValue)){
			$strValue = implode($strValue);
		}
		if (preg_match("/" . $arrReq . "/s", $strValue) == 1){   
			echo '<pre><font color="red"><b>Input illegal!</b></font></pre>';
			exit();
		}
	}
	
	
}