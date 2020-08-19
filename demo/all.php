<?php
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new BatchConsumer($server); //批量
// $consumer = new SyncConsumer($server); // 同步
// $consumer = new FileConsumer('./log'); // 存文件

$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(1);

$distinctId = '111111';
$isLogin = true;
$eventName = 'eventName';
$platform = 'JS';
$bookList = array(
    'is AnalysysAgent PHP SDK',
);

$track_properties = array(
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

$ans->track($distinctId, $isLogin, $eventName, $track_properties, $platform, $xwhen);

$registerId = 'ABCDEF123456789';
$ans->alias($registerId, $distinctId, $platform, $xwhen);

$fileSet_properties = array(
    '$city' => '北京',
    '$province' => '北京',
    'nickName' => '昵称123',
    'userLevel' => 0,
    'userPoint' => 0,
    'interest' => array(
        '户外活动',
        '足球赛事',
        '游戏',
    ),
);
$ans->profileSet($registerId, $isLogin, $fileSet_properties, $platform, $xwhen);

$fileSetOnce_properties = array(
    'registerTime' => '20180101101010',
);
$ans->profileSetOnce($registerId, $isLogin, $fileSetOnce_properties, $platform, $xwhen);

$fileIncrement_properties = array(
    'userPoint' => 20,
);
$ans->profileIncrement($registerId, $isLogin, $fileIncrement_properties, $platform, $xwhen);

$fileAppend_properties = array(
    'interest' => array(
        '户外活动',
        '足球赛事',
        '游戏',
    ),
);
$ans->profileAppend($registerId, $isLogin, $fileAppend_properties, $platform, $xwhen);

$ans->profileUnSet($registerId, $isLogin, "nickName", $platform, $xwhen);

$ans->profileDelete($registerId, $isLogin, $platform, $xwhen);

$registerSuperProperties_properties = array(
    'userLevel' => 0,
    'userPoint' => 0,
);
$ans->registerSuperProperties($registerSuperProperties_properties);

$ans->unRegisterSuperProperty('userPoint');

$ans->clearSuperProperties();

$getSuperProperty = $ans->getSuperProperty('userLevel');
printf('getSuperProperty--->%s', $getSuperProperty);

$properties = $ans->getSuperProperties();
printf('getSuperProperties---->');
print_r($properties);
$ret = $ans->flush(); //批量
print_r('API status:');
var_dump($ret);
