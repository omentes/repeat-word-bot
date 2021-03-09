<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot = Bot::getInstance();
$bot->init($config, $logger);
$metric = Metric::getInstance()->init($config);
Cache::getInstance()->init($config);
$bot->runHook();
echo json_encode(
    ["repeat-bot" => "ok"]
);
