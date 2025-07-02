<?php

namespace App\Service;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\TelegramMessageResponse\TelegramMessageCollection;
use App\Enum\MessageView;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class TelegramMessageFactory implements MessageFactoryInterface
{
    public function __construct(
        #[AutowireIterator(tag: 'telegram_viewer')]
        private iterable $viewers,
    ) {
    }

    public function create(ViewDataCollection $collection): TelegramMessageCollection
    {
        $telegramMessageCollection = new TelegramMessageCollection();

        foreach ($collection->toArray() as $viewData) {
            $identifier = MessageViewIdentifier::fromView($viewData->getCurrentView());

            foreach ($this->viewers as $viewer) {
                if ($viewer->supports($identifier)) {
                    $telegramMessageCollection->add($viewer->render($viewData));
                }
            }
        }

        return $telegramMessageCollection;
    }
}
