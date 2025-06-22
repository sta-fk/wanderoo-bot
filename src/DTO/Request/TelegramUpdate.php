<?php

namespace App\DTO\Request;

use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use Symfony\Component\Serializer\Attribute\SerializedName;

class TelegramUpdate
{
    public ?TelegramMessage $message = null;

    #[SerializedName("callback_query")]
    public ?TelegramCallbackQuery $callbackQuery = null;

    public function getChatId(): ?int
    {
        return match (true) {
            null !== $this->message => $this->message->chat->id,
            null !== $this->callbackQuery => $this->callbackQuery->message->chat->id,
            default => null
        };
    }

    public function getLanguageCode(): ?string
    {
        return match (true) {
            null !== $this->message => $this->message->from->languageCode,
            null !== $this->callbackQuery => $this->callbackQuery->from->languageCode,
            default => null,
        };
    }

    public function getCustomCallbackQueryData(CallbackQueryData $callbackQueryData): ?string
    {
        return substr($this->callbackQuery->data, strlen($callbackQueryData->value));
    }

    public function getCallbackMessageId(): int
    {
        if (null !== $this->callbackQuery) {
            return $this->callbackQuery->message->messageId;
        }

        throw new \RuntimeException('Unsupported CallbackQuery object');
    }

    public function supportsCallbackQuery(CallbackQueryData $callbackQueryData): bool
    {
        if (null === $this->callbackQuery) {
            return false;
        }

        if ($callbackQueryData->value === $this->callbackQuery->data) {
            return true;
        }

        if (str_starts_with($this->callbackQuery->data, $callbackQueryData->value)) {
            return true;
        }

        return false;
    }

    public function isMessageUpdate(): bool
    {
        return null !== $this->message;
    }

    public function supportsMessageUpdate(TelegramCommands $commands): bool
    {
        if (null === $this->message) {
            return false;
        }

        if ($commands->value === $this->message->text) {
            return true;
        }

        return false;
    }
}
