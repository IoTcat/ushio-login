<?php

$redis = new redis();
$redis->connect('redis',6379);


$email = $_REQUEST['email'];
if(!isset($email)) die();


$code = rand(100000, 999999);

$redis->set('vercode/'.$email, $code);
$redis->expire('vercode/'.$email, 600);

$url = 'https://api.yimian.xyz/mail/?to='.$email;
$url .= '&from=Ushio-Login&subject=Ushio登录验证码&body=您的验证码为'.$code;
$url .= '，请在10分钟内填写！';


$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 500);
curl_setopt($curl, CURLOPT_URL, $url);
$res = curl_exec($curl);
curl_close($curl);

