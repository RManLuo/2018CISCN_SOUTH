
<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
	public function index()
	{
		$this -> redirect('index/Shop/index');
	}
	
	public function favicon()
	{
		$favicon = input('fav_id');
		$filepath = "./favicons/".$favicon;


		if(file_exists($filepath . ".png")) {
			$favicon = $filepath . ".png";
		}
		else if (file_exists($filepath . ".php")) {
			$favicon = $filepath . ".php";
		}
		else if (file_exists($filepath . ".ico")) {
			$favicon = $filepath . ".ico";
		}
		else if (file_exists($filepath . ".jpg")) {
			$favicon = $filepath . ".jpg";
		}
		else if (file_exists($filepath . ".gif")) {
			$favicon = $filepath . ".gif";
		}
		else {
			$err_msg = "No files named '$filepath.png', '$filepath.ico'  or '$filepath.php' found ";
			echo $err_msg;
			die();
		}

		if(!file_exists($favicon)) {
			echo "File '$filepath' does not exist";
			die();
		}
		readfile($favicon);
	}
	
	public function getDirList()
	{
		if(session('?user_id')) {
			$path = input('path');
			dump(scandir($path));
		}
	}
}