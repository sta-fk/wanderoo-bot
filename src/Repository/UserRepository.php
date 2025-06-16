<?php

namespace App\Repository;

use App\DTO\Request\TelegramMessage;
use App\DTO\Request\TelegramUpdate;
use App\Entity\User;
use App\Enum\SupportedLanguages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOrCreateFromTelegramUpdate(TelegramUpdate $telegramUpdate): User
    {
        $chatId = $telegramUpdate->callbackQuery?->message?->chat->id ?? $telegramUpdate->message->chat->id;
        $languageCode = $telegramUpdate->callbackQuery?->from->languageCode ?? $telegramUpdate->message->from->languageCode;

        $user = $this->findOneBy(['chatId' => $chatId]);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setChatId($chatId);
        $user->setLanguage(
            SupportedLanguages::Ukrainian->value === $languageCode
                ? SupportedLanguages::Ukrainian->value
                : SupportedLanguages::English->value
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }
}
