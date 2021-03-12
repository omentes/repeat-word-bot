<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
Database::getInstance()->init($config);
$bot = Bot::getInstance();
$bot->init($config, $logger);

$metric = Metric::getInstance()->init($config);

while (true) {
    $bot->botNotify();
    usleep(500000);
    $metric->increaseMetric('notify');
}

