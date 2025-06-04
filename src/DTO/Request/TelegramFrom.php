<?php

namespace App\DTO\Request;

class TelegramFrom
{
    public int $id;
    public bool $isBot;
    public string $firstName;
    public string $username;
    public string $languageCode;
}
