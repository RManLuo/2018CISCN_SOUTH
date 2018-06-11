
<?php
namespace app\index\controller;
use think\Controller;
class Captcha extends Controller
{
	protected $jpgsPath = "../application/data/captcha/jpgs";
	protected $ansPath = "../application/data/captcha/ans";
	
    public function getCaptchaUuidList()
    {
		$fileList = scandir($this -> jpgsPath);
		unset($fileList[0], $fileList[1]);
		foreach($fileList as $key => $val) {
			$uuidList[] = str_replace("ques", "", str_replace(".jpg", "", $val));
		}
		return $uuidList;
    }
	
	public function getCaptcha()
	{
		$captchaUuidList = $this -> getCaptchaUuidList();
		$captchaUuid = $captchaUuidList[array_rand($captchaUuidList)];
		$captchaAnsFileContent = file($this -> ansPath . "/ans" . $captchaUuid . ".txt"); 
		$captchaQuestion = str_replace("vtt_ques = ", "", $captchaAnsFileContent[count($captchaAnsFileContent) - 1]);
		session('captchaUuid', $captchaUuid);
		$res['uuid'] = $captchaUuid;
		$res['question'] = $captchaQuestion;
		return $res;
	}
	
	public function showCaptchaImage()
	{
		usleep(10000);
		$captchaUuid = session('captchaUuid');
		$captchaImagePath = $this -> jpgsPath . "/ques" . $captchaUuid . ".jpg";
		ob_end_clean();
		header( "Content-type: image/jpeg");
		$captchaImageSize = filesize($captchaImagePath);
		$captchaImageData = fread(fopen($captchaImagePath, "r"), $captchaImageSize);
		echo $captchaImageData;
	}
	
	public function validateCaptcha($captchaX, $captchaY)
	{	
		$captchaUuid = session('captchaUuid');
		$captchaAnsFileContent = file($this -> ansPath . "/ans" . $captchaUuid . ".txt"); 
		$captchaAnsXPosition = (float)str_replace("ans_pos_x_1 = ", "", $captchaAnsFileContent[0]);
		$captchaAnsYPosition = (float)str_replace("ans_pos_y_1 = ", "", $captchaAnsFileContent[1]);
		$captchaAnsXWidth = (float)str_replace("ans_width_x_1 = ", "", $captchaAnsFileContent[2]);
		$captchaAnsYHeight = (float)str_replace("ans_height_y_1 = ", "", $captchaAnsFileContent[3]);
		$captchaX = (float)$captchaX;
		$captchaY = (float)$captchaY;
		if($captchaX >= $captchaAnsXPosition && $captchaX <= ($captchaAnsXPosition + $captchaAnsXWidth) && $captchaY >= $captchaAnsYPosition && $captchaY <= $captchaAnsYPosition + $captchaAnsYHeight) {
			return TRUE;
		}
		return FALSE;
	}
	
}
