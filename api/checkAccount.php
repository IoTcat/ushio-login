<?php

include './functions.php';

function updateSession($cnn, $hash, $redis){
    $res = db__getData($cnn, "account", "hash", $hash);
    $arr = $res[0];
    foreach($arr as $key => $val){
        $redis->hSet('session/dialog/'.$hash, $key, $val);
    }
}


$redis = new redis();
$redis->connect('redis',6379);


$hash = $_REQUEST['hash'];

if(!isset($hash)) die();



if($redis->exists('account/'.$hash)){

    $cnn = db__connect();
	$arr = json_decode($redis->get('account/'.$hash), true);

	if(!$arr['isExist']){
		$arr_t = $arr;
        foreach($arr_t as $i => $t){
            if($t == ''){
                unset($arr_t[$i]);
            }
        }
		unset($arr_t['isExist']);
		$cnn = db__connect();
		db__pushData($cnn, "account", $arr_t);
		$arr['isExist'] = true;
		$redis->set('account/'.$hash, json_encode($arr));
	}

    //$token = hash('sha256', $hash.time());
    //$redis->set('auth/token/'.$token, $hash);

    updateSession($cnn, $hash, $redis);   
    echo json_encode(array("code"=> 200, "token"=>$hash, "message" => "Verified successfully!"));

}else{
    echo json_encode(array("code"=> 500, "message"=>"Error!!"));
}
