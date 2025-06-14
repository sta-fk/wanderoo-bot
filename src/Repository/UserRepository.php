<?php

namespace App\Repository;

use App\DTO\Request\TelegramMessage;
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

    public function findOrCreateFromTelegramUser(TelegramMessage $telegramMessage): User
    {
        $user = $this->findOneBy(['chatId' => $telegramMessage->chat->id]);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setChatId($telegramMessage->chat->id);
        $user->setLanguage(
            SupportedLanguages::Ukrainian->value === $telegramMessage->from->languageCode
                ? SupportedLanguages::Ukrainian->value
                : SupportedLanguages::English->value
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }
}
