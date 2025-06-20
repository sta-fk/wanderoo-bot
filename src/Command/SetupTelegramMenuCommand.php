<?php

namespace App\Command;

use App\Enum\SupportedLanguages;
use App\Service\TelegramCommandMenuService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:telegram:setup-menu')]
class SetupTelegramMenuCommand extends Command
{
    public function __construct(
        private readonly TelegramCommandMenuService $menuService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->menuService->setDefaultMenuButton();
        foreach (SupportedLanguages::cases() as $language) {
            $this->menuService->setCommandsForLanguage($language);
        }

        $output->writeln('<info>Telegram menu initialized successfully ✅</info>');

        return Command::SUCCESS;
    }
}
