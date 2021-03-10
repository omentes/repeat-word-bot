<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Prometheus\Exception\MetricsRegistrationException;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class AlarmCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class AlarmCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'alarm';
    /**
     * @var string
     */
    protected $description = 'Alarm command';
    /**
     * @var string
     */
    protected $usage = '/alarm';
    /**
     * @var string
     */
    protected $version = '1.0.0';
    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     * @throws MetricsRegistrationException
     */
    public function execute(): ServerResponse
    {
        $input = $this->getMessage()->getText(true);
        $text = null === $input ? '' : $input;
        $command = new CommandDirector(
            new CommandOptions(
                'alarm',
                explode(' ', $text),
                $this->getMessage()->getChat()->getId()
            )
        );
        $service = $command->makeService();

        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }
}
