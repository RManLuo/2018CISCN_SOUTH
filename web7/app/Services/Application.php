<?php

namespace App\Services;

Class Application
{
    public function __construct()
    {
        $this->root_path = resource_path();
        $this->ans_path = $this->root_path . "/captcha/ans/";
        $this->jpgs_path = $this->root_path . "/captcha/jpgs/";
        $this->files = $this->_get_files($this->jpgs_path);
    }

    public function _get_files($file_path)
    {
        $files = [];
        $dir = new \DirectoryIterator($file_path);
        foreach ($dir as $file) {
            if ($file->isFile()) {
                array_push($files, $file->getFilename());
            }
        }
        return $files;
    }

    public function _get_ans($uuid)
    {
        $answer = [];
        $f = fopen($this->ans_path . sprintf("ans%s.txt", $uuid), 'r');
        while (!feof($f)) {
            $line = fgets($f);
            if ($f !== '\n') {
                $ans = explode("=", preg_replace('/\s/', "", $line));
                @$answer[trim($ans[0])] = trim($ans[1]);

            }
        }
        return $answer;
    }

    public function _generate_captcha()
    {
        $uuids = [];
        foreach ($this->files as $file) {
            array_push($uuids, str_replace(".jpg", "", str_replace("ques", "", $file)));
        }
        $uuid = $uuids[array_rand($uuids, 1)];
        $ans = $this->_get_ans($uuid);
        session(['uuid' => $uuid, 'question' => $ans["vtt_ques"]]);
    }

}