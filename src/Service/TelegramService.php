<?php

namespace App\Service;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Service\CommandsService\ViewGeneratedTripService;
use App\Service\FlowStepService\FinalStateAwareFlowStepServiceInterface;
use App\Service\FlowStepService\GeneratingTripPlanService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramService
{
    private string $apiUrl;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
        private readonly UserStateStorage $userStateStorage,
        private readonly FlowRegistry $flowRegistry,
        ParameterBagInterface $params,
    ) {
        $this->apiUrl = sprintf('%s%s', $params->get('telegram_bot_api_url'), $params->get('telegram_bot_token'));
    }

    public function handleUpdate(TelegramUpdate $update): void
    {
        $flowStepService = $this->flowRegistry->findMatchingService($update);

        if ($flowStepService instanceof FinalStateAwareFlowStepServiceInterface) {
            $messages = $flowStepService->getSplitFormattedPlan($update);
            foreach ($messages as $message) {
                if (mb_strlen($message->text) > 4096) {
                    throw new \RuntimeException('Telegram message too long: ' . mb_strlen($message->text));
                }

                $this->sendMarkdownMessage($message);
                usleep(3000);
            }

            return;
        }

        if (null !== $flowStepService) {
            $message = $flowStepService->buildNextStepMessage($update);

            $this->sendMarkdownMessage($message);
            $this->updateState($message);

            return;
        }

        $chatId = $update->callbackQuery->message->chat->id ?? $update->message->chat->id ?? null;
        if (null === $chatId) {
            throw new \RuntimeException('Invalid payload');
        }

        $this->sendMarkdownMessage(new SendMessageContext($chatId, 'Не впізнаний запит'));
    }

    public function sendMarkdownMessage(SendMessageContext $message): void
    {
        $payload = [
            'chat_id' => $message->chatId,
            'text' => $message->text,
            'parse_mode' => 'HTML',
        ];

        if ($message->replyMarkup) {
            $payload['reply_markup'] = $this->serializer->serialize($message->replyMarkup, 'json', [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);
        }

        $this->httpClient->request(
            'POST',
            "{$this->apiUrl}/sendMessage",
            [
            'json' => $payload,
            ]
        );
    }

    private function updateState(SendMessageContext $message): void
    {
        if (null === $message->nextState) {
            return;
        }

        $this->userStateStorage->updateState($message->chatId, $message->nextState);
    }
}
