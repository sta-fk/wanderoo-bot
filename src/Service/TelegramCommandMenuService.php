<?php

namespace App\Service;

use App\Enum\SupportedLanguages;
use App\Enum\TelegramCommands;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class TelegramCommandMenuService
{
    private string $apiUrl;

    public function __construct(
        private TranslatorInterface $translator,
        private HttpClientInterface $httpClient,
        ParameterBagInterface $params,
    ) {
        $this->apiUrl = sprintf('%s%s', $params->get('telegram_bot_api_url'), $params->get('telegram_bot_token'));
    }

    public function setCommandsForLanguage(SupportedLanguages $language): void
    {
        $commands = [
                ['command' => TelegramCommands::Start->value, 'description' => $this->translator->trans(id: 'commands.start', locale: $language->value)],
                ['command' => TelegramCommands::StartNew->value, 'description' => $this->translator->trans(id: 'commands.start_new', locale: $language->value)],
                ['command' => TelegramCommands::ViewSavedPlansList->value, 'description' => $this->translator->trans(id: 'commands.view_saved_plans_list', locale: $language->value)],
                ['command' => TelegramCommands::Settings->value, 'description' => $this->translator->trans(id: 'commands.settings', locale: $language->value)],
                ['command' => TelegramCommands::ViewCurrentDraftPlan->value, 'description' => $this->translator->trans(id: 'commands.view_current_draft_plan', locale: $language->value)],
        ];

        $this->httpClient->request('POST', "{$this->apiUrl}/setMyCommands", [
            'json' => [
                'commands' => $commands,
                'language_code' => $language->value,
            ]
        ]);
    }

    public function setDefaultMenuButton(): void
    {
        $this->httpClient->request('POST', "{$this->apiUrl}/setChatMenuButton", [
            'json' => [
                'menu_button' => [
                    'type' => 'commands',
                ],
            ]
        ]);
    }
}
