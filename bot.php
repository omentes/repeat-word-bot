<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot = Bot::getInstance();
$bot->init($config, $logger);
$expectedTime = time() + 10 * 60;
$metric = Metric::getInstance()->init($config);
while (true) {
    $bot->run();
    $metric->increaseMetric('worker');
    if ($expectedTime <= time()) {
        die(0);
    }
}

