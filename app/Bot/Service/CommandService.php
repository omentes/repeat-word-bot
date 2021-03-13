<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service;

use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService\CallbackQueryDirectorFabric;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\CommandInterface;
use RepeatBot\Bot\Service\CommandService\GenericMessageDirectorFabric;

/**
 * Class CommandService
 * @package RepeatBot\Bot\Service\CommandService
 */
class CommandService
{
    public function __construct(protected CommandOptions $options, protected string $type = '')
    {
    }

    /**
     * @return CommandInterface
     */
    public function makeService(): CommandInterface
    {
        return match ($this->getType()) {
            'query' => $this->makeQueryService($this->getOptions()),
            'generic' => $this->makeGenericService($this->getOptions()),
        default => $this->makeDefaultService($this->getOptions()),
        };
    }


    /**
     * @param CommandInterface $service
     *
     * @return ServerResponse
     */
    public function executeCommand(CommandInterface $service): ServerResponse
    {
        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeDefaultService(CommandOptions $options): CommandInterface
    {
        $command = new CommandDirector($options);

        return $command->makeService();
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeQueryService(CommandOptions $options): CommandInterface
    {
        $command = (new CallbackQueryDirectorFabric($options))->getCommandDirector();

        return $command->makeService();
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeGenericService(CommandOptions $options): CommandInterface
    {
        $command = (new GenericMessageDirectorFabric($options))->getCommandDirector();

        return $command->makeService();
    }

    /**
     * @return CommandOptions
     */
    public function getOptions(): CommandOptions
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}