<?php

namespace App\Service;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\TelegramView\TelegramViewInterface;
use App\DTO\Internal\ViewData\ViewDataInterface;
use App\DTO\TelegramMessage\TelegramMessageInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class TelegramMessageFactory implements MessageFactoryInterface
{
    public function __construct(
        #[AutowireIterator(tag: 'telegram.view')]
        private iterable $views
    ) {}

    public function create(MessageViewIdentifier $identifier, ViewDataInterface $data): TelegramMessageInterface
    {
        /** @var TelegramViewInterface $view */
        foreach ($this->views as $view) {
            if ($view->supports($identifier)) {
                return $view->render($data);
            }
        }

        throw new \RuntimeException("No view found for: {$identifier->value}");
    }
}

