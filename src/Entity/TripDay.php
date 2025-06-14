<?php

namespace App\Entity;

use App\Repository\TripDayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: TripDayRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TripDay
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: TripStop::class, inversedBy: 'days')]
    #[ORM\JoinColumn(nullable: false)]
    private TripStop $stop;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $dayIndex;

    #[ORM\Column(type: Types::JSON)]
    private array $activities = [];

    #[ORM\Column(type: Types::JSON)]
    private array $foodPlaces = [];

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $date;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStop(): TripStop
    {
        return $this->stop;
    }

    public function getDayIndex(): int
    {
        return $this->dayIndex;
    }

    public function getActivities(): array
    {
        return $this->activities;
    }

    public function getFoodPlaces(): array
    {
        return $this->foodPlaces;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setStop(TripStop $stop): void
    {
        $this->stop = $stop;
    }

    public function setDayIndex(int $dayIndex): void
    {
        $this->dayIndex = $dayIndex;
    }

    public function setActivities(array $activities): void
    {
        $this->activities = $activities;
    }

    public function setFoodPlaces(array $foodPlaces): void
    {
        $this->foodPlaces = $foodPlaces;
    }

    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }
}
