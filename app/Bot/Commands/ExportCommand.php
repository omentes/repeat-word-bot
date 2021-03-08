<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Core\App;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Metric;
use RepeatBot\Core\ORM\Entities\Export;

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
        $director = new CommandDirector(
            new CommandOptions(
                'export',
                explode(' ', $this->getMessage()->getText(true)),
                $this->getMessage()->getChat()->getId(),
            )
        );
        $service = $director->makeService();
    
        if (!$service->hasResponse()) {
            $service->execute();
        }
    
        return $service->postStackMessages()->getResponseMessage();
    }
}
