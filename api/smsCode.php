<?php

$redis = new redis();
$redis->connect('redis',6379);


$tel = $_REQUEST['tel'];
if(!isset($tel)) die();



$code = rand(100000, 999999);

$redis->set('vercode/'.$tel, $code);
$redis->expire('vercode/'.$tel, 300);

$url = 'https://api.yimian.xyz/sms/?to='.$tel;
$url .= '&template=3&s0=Ushio-Login&s1=验证码&s2='.$code;
$url .= '&t='.time();


$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 500);
curl_setopt($curl, CURLOPT_URL, $url);
$res = curl_exec($curl);
curl_close($curl);

echo json_encode(array(
	"code"=> 200
));
