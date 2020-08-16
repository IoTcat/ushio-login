<?php

include './functions.php';

function updateSession($cnn, $hash){
    $res = db__getData($cnn, "account", "hash", $hash);
    $arr = $res[0];
    foreach($arr as $key => $val){
        $redis->hSet('session/dialog/'.$hash, $key, $val);
    }
}


$redis = new redis();
$redis->connect('redis',6379);


$hash = $_REQUEST['hash'];
$nickname = $_REQUEST['nickname'];
$thash = $_REQUEST['thash'];

if(!isset($hash)) die();

$cnn = db__connect();
if(!db__rowNum($cnn, "account", "hash", $hash)){
    echo json_encode(array("code"=> 500, "message"=>"Illegal hash!!"));
    die();
}

if(isset($nickname)){

    db__pushData($cnn, "account", array("nickname"=>$nickname), array("hash"=>$hash));
    updateSession($cnn, $hash);
    echo json_encode(array("code"=> 200, "message" => "Update successfully!"));
    die();
}

if(!isset($thash)) die();

if($redis->exists('account/'.$thash)){

	$arr = json_decode($redis->get('account/'.$thash), true);

	if($arr['isExist']){
		echo json_encode(array("code"=> 500, "message"=>"This tel / email had been occupied!!"));
        die();
	}


    $info = array();
    if($arr["tel"]){
        $info = array("tel"=>$arr["tel"]);
    }
    if($arr["email"]){
        $info = array("email"=>$arr["email"]);
    }
    if($arr["nickname"]){
        $info = array("nickname"=>$arr["nickname"]);
    }

    db__pushData($cnn, "account", $info, array("hash"=>$hash));
    updateSession($cnn, $hash);
    echo json_encode(array("code"=> 200, "message" => "Update successfully!"));

}else{
    echo json_encode(array("code"=> 500, "message"=>"Error!!"));
}


