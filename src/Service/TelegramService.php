<?php

namespace App\Service;

use App\DTO\Request\TelegramUpdate;
use App\DTO\TelegramMessage\AnswerCallbackQueryContext;
use App\DTO\TelegramMessage\DeleteMessageContext;
use App\DTO\TelegramMessage\EditMessageTextContext;
use App\DTO\TelegramMessage\SendDocumentContext;
use App\DTO\TelegramMessage\SendMessageContext;
use App\DTO\TelegramMessage\SendPhotoContext;
use App\DTO\TelegramMessage\TelegramMessageInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TelegramService
{
    private string $apiUrl;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly TelegramMessageFactory $messageFactory,
        private readonly FlowRegistry $flowRegistry,
        private readonly UserStateStorage $stateStorage,
        private readonly SerializerInterface $serializer,
        ParameterBagInterface $params,
    ) {
        $this->apiUrl = sprintf('%s%s', $params->get('telegram_bot_api_url'), $params->get('telegram_bot_token'));
    }

    public function handleUpdate(TelegramUpdate $update): void
    {
        $chatId = $update->callbackQuery?->message->chat->id ?? $update->message?->chat->id ?? null;
        if (!$chatId) {
            return;
        }

        $flowStepService = $this->flowRegistry->findMatchingService($update);
        if (null === $flowStepService) {
            return;
        }

        $viewData = $flowStepService->buildViewData($update);
        $identifier = $this->flowRegistry->resolveMessageViewIdentifier($viewData->getCurrentView());

        $messages = $this->messageFactory->create($identifier, $viewData);

        foreach ($messages as $message) {
            $this->sendMessage($message);
        }

        $this->stateStorage->updateState($chatId, $viewData->getState());
    }

    public function sendMessage(TelegramMessageInterface $message): void
    {
        match (true) {
            $message instanceof SendMessageContext => $this->sendText($message),
            $message instanceof SendPhotoContext => $this->sendPhoto($message),
            $message instanceof SendDocumentContext => $this->sendDocument($message),
            $message instanceof EditMessageTextContext => $this->editMessageText($message),
            $message instanceof DeleteMessageContext => $this->deleteMessage($message),
            $message instanceof AnswerCallbackQueryContext => $this->answerCallback($message),
            default => throw new \InvalidArgumentException('Unsupported Telegram message type'),
        };
    }

    private function sendText(SendMessageContext $message): void
    {
        $this->requestToTelegram('/sendMessage', [
            'chat_id' => $message->chatId,
            'text' => $message->text,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->maybeSerializeReplyMarkup($message->replyMarkup),
        ]);
    }

    private function sendPhoto(SendPhotoContext $message): void
    {
        $this->requestToTelegram('/sendPhoto', [
            'chat_id' => $message->chatId,
            'photo' => $message->photoUrl,
            'caption' => $message->caption,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->maybeSerializeReplyMarkup($message->replyMarkup),
        ]);
    }

    private function sendDocument(SendDocumentContext $message): void
    {
        $this->requestToTelegram('/sendDocument', [
            'chat_id' => $message->chatId,
            'document' => $message->documentUrl,
            'caption' => $message->caption,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->maybeSerializeReplyMarkup($message->replyMarkup),
        ]);
    }

    private function editMessageText(EditMessageTextContext $message): void
    {
        $this->requestToTelegram('/editMessageText', [
            'chat_id' => $message->chatId,
            'message_id' => $message->messageId,
            'text' => $message->text,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->maybeSerializeReplyMarkup($message->replyMarkup),
        ]);
    }

    private function deleteMessage(DeleteMessageContext $message): void
    {
        $this->requestToTelegram('/deleteMessage', [
            'chat_id' => $message->chatId,
            'message_id' => $message->messageId,
        ]);
    }

    private function answerCallback(AnswerCallbackQueryContext $message): void
    {
        $this->requestToTelegram('/answerCallbackQuery', [
            'callback_query_id' => $message->callbackQueryId,
            'text' => $message->text,
            'show_alert' => $message->showAlert,
        ]);
    }

    private function requestToTelegram(string $method, array $payload): void
    {
        // Видаляємо null поля
        $filteredPayload = array_filter($payload, static fn($v) => null !== $v);

        $this->httpClient->request('POST', $this->apiUrl . $method, [
            'json' => $filteredPayload,
        ]);
    }

    private function maybeSerializeReplyMarkup(?array $markup): ?string
    {
        return $markup
            ? $this->serializer->serialize($markup, 'json', [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true])
            : null;
    }
}
