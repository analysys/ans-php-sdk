<?php
require_once 'AnalysysAgent_PHP_SDK.php';
date_default_timezone_set('prc');

$appid = '9421608fd544a65e';
$async = true;
$batch_num = 100;
$rule = 'HOUR';
$file_path = './log';
$consumer = new FileConsumer($file_path, $rule, $async, $batch_num);

$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(0);

function run($i = 0, $ans)
{

    $properties = array('aaa' => 111, 'ccc' => 'sssddd');
    $ans->track('aaa' . $i, false, 'gaolaoshi', $properties);
    $ans->track('bbb' . $i, false, 'gaolaoshi', $properties);
    $ans->track('ccc' . $i, false, 'gaolaoshi', $properties);
    $ans->track('ddd' . $i, false, 'gaolaoshi', $properties);
    $ans->track('eee' . $i, false, 'gaolaoshi', $properties);
}

for ($i = 0; $i < 100; $i++) {
    run($i, $ans);

}
$ans->flush();
