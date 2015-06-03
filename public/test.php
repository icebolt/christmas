<?php
/**
 * Created by PhpStorm.
 * User: fanzhanao
 * Date: 15/6/3
 * Time: 下午4:08
 */
set_time_limit(0);

function genRandomString($length = 10) {
    $characters = str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function genDeviceToken(){
    $token = genRandomString(8).'-'.genRandomString(4).'-'.genRandomString(4).'-'.genRandomString(4).'-'.genRandomString(12);
    return strtoupper($token);
}

function request(){
    $curl = curl_init();
    $token = genDeviceToken();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://activity.bb.bbwc.cn/prize/win',
        CURLOPT_USERAGENT => 'Test Lottery',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array(
            deviceid => genDeviceToken(),
            uid => '2'
        )
    ));
    $resp = curl_exec($curl);
    if ($resp){
        $response =  json_decode($resp);
        $data = $response->data;
        if ($data){
            echo "=======中奖了===========\n";
            echo "#####中奖ID:{$token}######\n";
        }
    }
    curl_close($curl);
}
for ($i=0;$i<10000;$i++){
    request();
    sleep(1);
}


