<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Export;
use RepeatBot\Core\ORM\Repositories\ExportRepository;

/**
 * Class ExportValidator
 * @package RepeatBot\Bot\Service\CommandService\Validators
 */
class ExportValidator implements ValidateCommand
{
    private ExportRepository $exportRepository;

    /**
     * ExportValidator constructor.
     */
    public function __construct()
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->exportRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Export::class);
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function validate(CommandOptions $options): array
    {
        if ($this->exportRepository->userHaveExport($options->getChatId())) {
            return $this->createUserHaveExportResponse($options);
        }

        $payload = $options->getPayload();
        if (
            count($payload) === 2 &&
            (
                !in_array($payload[0], ['FromEnglish','ToEnglish']) ||
                !in_array($payload[1], ['first','second','third','fourth','fifth','sixth','never'])
            ) ||
            count($payload) > 2
        ) {
            return $this->createInvalidPayloadReponse($options);
        }

        return [];
    }

    private function getHaveExportErrorText(): string
    {
        return 'У вас есть экспорт слов, дождитесь очереди для создания файла';
    }

    private function getInvalidPayloadErrorText(): string
    {
        return "Допустимые форматы команды\n - /export\n - /export FromEnglish first\n" .
            " - /export ToEnglish second\n\n Где первое слово режим без пробела, а второе название итерации. " .
            "Посмотреть сколько у вас слов в какой итерации можно командой /progress";
    }

    /**
     * @param CommandOptions $options
     *
     * @return ResponseDirector[]
     * @throws \Exception
     */
    private function createUserHaveExportResponse(CommandOptions $options): array
    {
        $data = [
            'chat_id' => $options->getChatId(),
            'text' => $this->getHaveExportErrorText(),
            'parse_mode' => 'markdown',
            'disable_notification' => 1,
        ];

        return [
            new ResponseDirector('sendMessage', $data)
        ];
    }

    /**
     * @param CommandOptions $options
     *
     * @return ResponseDirector[]
     * @throws \Exception
     */
    private function createInvalidPayloadReponse(CommandOptions $options): array
    {
        $data = [
            'chat_id' => $options->getChatId(),
            'text' => $this->getInvalidPayloadErrorText(),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];

        return [
            new ResponseDirector('sendMessage', $data)
        ];
    }
}
