<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Exception\ModuleException;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\DelService;
use RepeatBot\Bot\Service\CommandService\Commands\ExportService;
use RepeatBot\Bot\Service\CommandService\Messages\DelMessage;
use RepeatBot\Bot\Service\CommandService\Messages\ExportMessage;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Cache;
use RepeatBot\Core\ORM\Entities\Export;
use RepeatBot\Core\ORM\Entities\Training;
use UnitTester;

/**
 * Class ExportServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class ExportServiceTest extends Unit
{
    protected UnitTester $tester;
    protected EntityManager $em;

    protected function _setUp()
    {
        parent::_setUp();
        $this->em = $this->getModule('Doctrine2')->em;
    }

    /**
     * @param array $example
     * @dataProvider errorProvider
     */
    public function testExportValidator(array $example): void
    {
        $chatId = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'export',
                payload: explode(' ', $example['payload']),
                chatId: $chatId,
            )
        );

        if ($example['haveExport']) {
            $entity = new Export();
            $entity->setUserId($chatId);
            $entity->setWordType('FromEnglish');
            $entity->setChatId($chatId);
            $entity->setUsed(0);
            $this->tester->haveExportEntity($entity);
        }
        $service = $command->makeService();
        $this->assertInstanceOf(ExportService::class, $service);

        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => $example['message'],
            'parse_mode' => 'markdown',
            'disable_notification' => 1,
        ], $error->getData());
    }

    public function errorProvider(): array
    {
        return [
            [['payload' => '1', 'message' => ExportMessage::ERROR_INVALID_PAYLOAD_TEXT, 'haveExport' => false]],
            [['payload' => 'first', 'message' => ExportMessage::ERROR_INVALID_PAYLOAD_TEXT, 'haveExport' => false]],
            [['payload' => 'FromEnglish', 'message' => ExportMessage::ERROR_INVALID_PAYLOAD_TEXT, 'haveExport' => false]],
            [['payload' => 'FromEnglish 1', 'message' => ExportMessage::ERROR_INVALID_PAYLOAD_TEXT, 'haveExport' => false]],
            [['payload' => '', 'message' => ExportMessage::ERROR_HAVE_EXPORT_TEXT, 'haveExport' => true]],
        ];
    }
}