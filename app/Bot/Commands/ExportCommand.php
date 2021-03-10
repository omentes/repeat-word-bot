<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class ExportCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class ExportCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Export';
    /**
     * @var string
     */
    protected $description = 'Export command';
    /**
     * @var string
     */
    protected $usage = '/export';
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
     */
    public function execute(): ServerResponse
    {
        $input = $this->getMessage()->getText(true);
        $text = null === $input ? '' : $input;
        $director = new CommandDirector(
            new CommandOptions(
                'export',
                explode(' ', $text),
                $this->getMessage()->getChat()->getId(),
            )
        );
        $service = $director->makeService();

        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }
}
