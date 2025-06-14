<?php

namespace App\Service\KeyboardResolver;

use App\DTO\Request\TelegramCallbackQuery;
use App\DTO\Request\TelegramMessage;
use App\DTO\Request\TelegramUpdate;
use App\Service\KeyboardProvider\Callback\CallbackKeyboardProviderInterface;
use App\Service\KeyboardProvider\KeyboardProviderInterface;
use App\Service\KeyboardProvider\Message\MessageKeyboardProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class KeyboardProviderResolver
{
    public function __construct(
        #[AutowireIterator('callback_keyboard_provider')]
        private iterable $callbackKeyboardProviders,
        #[AutowireIterator('message_keyboard_provider')]
        private iterable $messageKeyboardProviders,
    ) {
    }

    public function resolve(TelegramUpdate $update): KeyboardProviderInterface
    {
        return match (true) {
            null !== $update->callbackQuery => $this->resolveCallbackKeyboardProvider($update->callbackQuery),
            null !== $update->message => $this->resolveMessageKeyboardProvider($update->message),
        };
    }

    private function resolveCallbackKeyboardProvider(TelegramCallbackQuery $callbackQuery): CallbackKeyboardProviderInterface
    {
        /** @var CallbackKeyboardProviderInterface $callbackKeyboardProvider */
        foreach ($this->callbackKeyboardProviders as $callbackKeyboardProvider) {
            if ($callbackKeyboardProvider->supports($callbackQuery)) {
                return $callbackKeyboardProvider;
            }
        }

        throw new \RuntimeException("Invalid callback");
    }

    private function resolveMessageKeyboardProvider(TelegramMessage $message): MessageKeyboardProviderInterface
    {
        /** @var MessageKeyboardProviderInterface $messageKeyboardProvider */
        foreach ($this->messageKeyboardProviders as $messageKeyboardProvider) {
            if ($messageKeyboardProvider->supports($message)) {
                return $messageKeyboardProvider;
            }
        }

        throw new \RuntimeException("Invalid message");
    }
}
