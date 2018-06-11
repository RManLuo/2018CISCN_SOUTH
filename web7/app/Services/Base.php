<?php

namespace App\Services;

use App\Services\Application;


Class Base
{
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function get_current_user()
    {
        return session("username");
    }

    public function check_captcha($captcha_x, $captcha_y)
    {
        try {
            $x = (float)$captcha_x;
            $y = (float)$captcha_y;
            if ($x && $y) {
                $uuid = session('uuid');
                $answer = $this->application->_get_ans($uuid);
                if ((float)$answer["ans_pos_x_1"] <= $x && $x <= (float)$answer["ans_width_x_1"]
                    + (float)$answer["ans_pos_x_1"]) {
                    if ((float)$answer["ans_pos_y_1"] <= $y && $y <= (float)$answer["ans_height_y_1"]
                        + (float)$answer["ans_pos_y_1"]) {
                        return true;
                    }
                }
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}