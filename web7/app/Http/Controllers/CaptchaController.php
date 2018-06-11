<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Application;

class CaptchaController extends Controller
{
    protected $application;

    public function captcha()
    {
        $this->application = new Application();
        $img = file_get_contents($this->application->jpgs_path."ques".session('uuid').".jpg");
        return $img;
    }
}
