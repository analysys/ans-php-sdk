<?php
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = 'heatmap0505';
$server = 'https://arkpaastest.analysys.cn:4089/';
// $consumer = new SyncConsumer($server); //同步
$consumer = new BatchConsumer($server); // 批量
$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(1);

$distinctId = '1234567890987654321';
$isLogin = true;
$eventName = 'eventName';
$platform = 'JS';
$bookList = array(
    'bookName' => 'is AnalysysAgent PHP SDK',
);
$properties = array(
    '$ip' => '112.112.112.112',
    'productType' => 'PHP书籍',
    'productName' => 'bookList',
    'producePrice' => '60',
    'shop' => '在线',
);

function msectime()
{
    list($msec, $sec) = explode(' ', microtime());
    return (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
}
$xwhen = msectime();

$ans->track($distinctId, $isLogin, $eventName, $properties, $platform, $xwhen);
$ans->track($distinctId, $isLogin, $eventName, $bookList, $platform, $xwhen);
$ans->flush() //批量
?>
