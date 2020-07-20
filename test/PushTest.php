<?php

require_once '../vendor/autoload.php';

use MobService\Push;

$app_key = '2fcb26f70ccc1';
$app_secret = '215a40b7b34095846677a7a34414b2df';

$Push = new Push($app_key, $app_secret);
$data = $Push->broadcastAndroid(' sdk push message', 'yunzhi://com.yzgl.news/web', 'id', 13);
echo($data);
