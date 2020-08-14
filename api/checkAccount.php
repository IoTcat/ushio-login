<?php

include './functions.php';

$redis = new redis();
$redis->connect('redis',6379);


$hash = $_REQUEST['hash'];

if(!isset($hash)) die();



if($redis->exists('account/'.$hash)){


	$arr = json_decode($redis->get('account/'.$hash));

	if(!$arr['isExist']){
		$arr_t = $arr;
		unset($arr_t['isExist']);
		$cnn = db__connect();
		db__pushData($cnn, "account", $arr_t);
		$arr['isExist'] = true;
		$redis->set('account/'.$arr['hash'], json_encode($arr));
	}


    echo json_encode(array("code"=> 200, "hash"=>$arr['hash'], "message" => "Verified successfully!"));

}else{
    echo json_encode(array("code"=> 500, "message"=>"Error!!"));
}
