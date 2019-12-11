<?php
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new SyncConsumer($server); //同步
//$consumer = new BatchConsumer($server); // 批量
$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(2);

$registerId = 'ABCDEF123456789';
$isLogin = true;
$platform = 'JS';
$properties = array(
    'userPoint' => 20,
);

function msectime()
{
    list($msec, $sec) = explode(' ', microtime());
    return (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
}
$xwhen = msectime();

$ans->profileIncrement($registerId, $isLogin, $properties, $platform, $xwhen);

//$ans->flush() //批量
