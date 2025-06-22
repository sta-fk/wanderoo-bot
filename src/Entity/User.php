<?php

namespace App\Entity;

use App\Enum\SupportedLanguages;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\Column(length: 3)]
    private string $language = SupportedLanguages::English->value;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column]
    private int $chatId;

    /** @var Collection<int, Trip> */
    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $trips;

    public function __construct()
    {
        $this->trips = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }

    /** @return Collection<int, Trip> */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function getDefaultCurrency(): ?string
    {
        return $this->currency;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function setDefaultCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function setTrips(Collection $trips): void
    {
        $this->trips = $trips;
    }

    public function addTrip(Trip $trip): static
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->setUser($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): static
    {
        if ($this->trips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getUser() === $this) {
                $trip->setUser(null);
            }
        }

        return $this;
    }
}
