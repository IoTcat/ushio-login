<?php

include './functions.php';

$redis = new redis();
$redis->connect('redis',6379);


$usr = $_REQUEST['usr'];
$code = $_REQUEST['code'];
$type = $_REQUEST['type'];


if(!isset($usr)) die();
if(!isset($code)) die();
if(!isset($type) || $type != 'tel' || $type != 'email') die();


if($redis->exists('vercode/'.$usr) && $redis->get('vercode/'.$usr) == $code){

	$cnn = db__connect();

	if($type == 'tel'){
		$res = db__getData($cnn, "account", "tel", $usr);
	}else{
		$res = db__getData($cnn, "account", "email", $usr);
	}

	if(count($res)){
		$arr = $res[0];
		$arr = array_merge($arr, array("isExist"=>true));
	}else{

		$arr = array(
			"nickname"=>null,
			"avatar"=>null,
			"tel"=>null,
			"email"=>null,
			"hash"=> hash('sha256', $usr.$code.$type.time()),
			"group"=>"client",
			"comments"=>"From ushio-login",
			"created_at"=>date("Y-m-d H:i:s", time()),
			"updated_at"=>date("Y-m-d H:i:s", time())

		);
		if($type == 'tel'){
			$arr['tel'] = $usr;
		}else{
			$arr['email'] = $usr;
		}
		$arr = array_merge($arr, array("isExist"=>false));
	}
	$redis->set('account/'.$arr['hash'], json_encode($arr));
    echo json_encode(array("code"=> 200, "hash"=>$arr['hash'], "isExist"=>$arr['isExist'], "message" => "Verified successfully!"));

}else{
    echo json_encode(array("code"=> 500, "message"=>"Error!!"));
}
