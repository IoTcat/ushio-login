<?php

$redis = new redis();
$redis->connect('redis',6379);


$usr = $_REQUEST['usr'];
$code = $_REQUEST['code'];


if(!isset($usr)) die();
if(!isset($code)) die();


if($redis->exists('vercode/'.$usr) && $redis->get('vercode/'.$usr) == $code){

    echo json_encode(array("code"=> 200, "message" => "Verified successfully!"));

}else{
    echo json_encode(array("code"=> 500, "message"=>"Error!!"));
}
