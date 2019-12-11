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

function msectime()
{
    list($msec, $sec) = explode(' ', microtime());
    return (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
}
$xwhen = msectime();

$ans->profileUnSet($registerId, $isLogin, "nickName", $platform, $xwhen);

// $ans->profileDelete($registerId,$isLogin,$platform);

//$ans->flush() //批量
