<?php

/**

 * filname      index.php
 * author       jinxin
 * Description  Description of index
 * Date         2014-3-19 18:33:48
 */
define('APPLICATION_PATH', dirname(__FILE__).'/../');

//ini_set('yaf.library', '/home/share/jinxin/yafLibrary');
if(!isset($_POST['raw']) && !empty($HTTP_RAW_POST_DATA)){
    $_POST['raw'] = $HTTP_RAW_POST_DATA;
}elseif(!isset($_POST['raw']) && isset ($_POST['data']) && !empty ($_POST['data'])){
    $_POST['raw'] = $_POST['data'];
}
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
$application = new Yaf\Application( APPLICATION_PATH . "/conf/application.ini");
$application->bootstrap()->run();