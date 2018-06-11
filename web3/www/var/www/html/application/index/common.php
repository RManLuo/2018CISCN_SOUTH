<?php
/**
 * Created by PhpStorm.
 * User: seaii
 * Date: 18-5-22
 * Time: 下午7:43
 */

function get_files($file_path) {
    $scan_res = scandir($file_path);
    foreach ($scan_res as $key=> $name) {
        if($name === '.' || $name === '..' || substr($name, 0, 1) === '.')
            unset($scan_res[$key]);
    }
    return $scan_res;
}

function gen_captcha() {
    $jpg_path = __DIR__.'/captcha/jpgs';
    $uuids = array();
    $files = get_files($jpg_path);
    foreach ($files as $file)
        $uuids[] = str_replace('.jpg', '', str_replace('ques', '', $file));
    $uuid = $uuids[mt_rand(0, count($uuids) - 1)];
    $answer = get_answer($uuid);

    $captcha = array(
        'uuid'=> $uuid,
        'question'=> $answer['vtt_ques'],
    );
    file_put_contents(__DIR__.'/../../runtime/captcha.temp', json_encode($captcha));
    //return array('uuid'=> $uuid, 'question'=> $answer['vtt_ques']);
}

function get_answer($uuid) {
    $answer = array();
    $ans_path = __DIR__.'/captcha/ans';
    $filename = $ans_path.'/ans'.$uuid.'.txt';
    $f = fopen($filename, 'r');
    while(!feof($f)) {
        $line = fgets($f);
        $ans = explode('=', trim($line));
        if(isset($ans[1]))
            $answer[trim($ans[0])] = trim($ans[1]);
    }
    return $answer;
}