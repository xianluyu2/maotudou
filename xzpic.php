<?php
error_reporting(0);
date_default_timezone_set("Asia/Shanghai");
header('Content-Type: text/html; charset=utf-8');
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('FCPATH', str_replace("\\", "/", str_replace(SELF, '', __FILE__)));
$id = $_GET['id'];
$name = $_GET['name'];
$page = ($_GET['page'])?$_GET['page']:1;

$json = getaurl('http://47.101.143.201:911/douban/?type=search&title='.$name);
$data = json_decode($json,1);
$pic = $data['data'][0]['image'];
if(!empty($pic)){
    $filePath = 'upload/vod/'.date('Y-m-d-H').'/'.time().'.jpg';
    $file = geturlimg($pic,'https://movie.douban.com/subject/'.$data['data'][0]['id'].'/');
    $pathinfo = pathinfo($filePath);
    $file_name = $pathinfo['basename'];
    $file_path = FCPATH.$pathinfo['dirname'];
    $files_path = $pathinfo['dirname'].'/'.$file_name;
    mkdirss($file_path);
    if(file_put_contents($file_path.'/'.$file_name,$file)){
        echo 'http://kb.troysung.com/'.$filePath;
    }else{
        echo 'no';
    }
}else{
    echo 'nopic';
}

// 字符串截取函数
function str_substr($start, $end, $str){
    $temp = @explode($start, $str, 2);
    $content = @explode($end, $temp[1], 2);
    return $content[0];
}

//获取远程内容
function getaurl($url,$header='',$post='',$ip=''){
    if(function_exists('curl_init')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if(!empty($post)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if(!empty($ip)){
            curl_setopt($ch, CURLOPT_PROXY, $ip);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//获取跳转后的
        $data = curl_exec($ch);
        curl_close($ch);
    }else{
        $data = 'curl no';
    }
    return $data;
}

//获取图片
function geturlimg($url,$re){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_REFERER, $re); //伪造来路页面
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回字符串，而非直接输出
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//获取跳转后的
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($code==301){
        $data = geturlimg(str_replace('http','https',$url),$re);
    }
    return $data;
}
//递归创建文件夹
function mkdirss($dir) {
    if (!$dir) {
        return FALSE;
    }
    if (!is_dir($dir)) {
        mkdirss(dirname($dir));
        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }
    }
    return true;
}
