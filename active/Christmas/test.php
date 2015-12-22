<?php
/**
 * Created by PhpStorm.
 * User: fanzhanao
 * Date: 15/12/22
 * Time: 上午11:30
 */

function httpPost($url,$params)
{
    $postData = '';
    foreach($params as $k => $v)
    {
        $postData .= $k . '='.$v.'&';
    }
    $postData = rtrim($postData, '&');
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, count($postData));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $output=curl_exec($ch);

    curl_close($ch);
    return $output;

}

$url = 'http://weixin.bbwc.cn/active/public/index.php/index/active/win';
$argumengts = array('uid'=>125,'token'=>"6580f9fe4ba60f3deb070c5c6676fb1f",'active_id'=>2);
$response = httpPost($url,$argumengts);
file_put_contents('/tmp/winprize.log',var_export($response,true)."\n",FILE_APPEND);

