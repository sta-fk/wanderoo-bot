<?php

namespace App\Entity;

use App\Repository\TripStopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: TripStopRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TripStop
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Trip::class, inversedBy: 'stops')]
    #[ORM\JoinColumn(nullable: false)]
    private Trip $trip;

    #[ORM\Column(length: 255)]
    private string $countryName;

    #[ORM\Column(length: 255)]
    private string $cityName;

    #[ORM\Column(length: 4)]
    private string $currency;

    #[ORM\Column]
    private float $budget;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $arrivalDate;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $departureDate;

    #[ORM\Column(type: Types::JSON)]
    private array $interests = [];

    #[ORM\Column(length: 255)]
    private string $tripStyle;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localTransport = null;

    /**
     * @var Collection<int, TripDay>
     */
    #[ORM\OneToMany(targetEntity: TripDay::class, mappedBy: 'stop')]
    private Collection $days;

    public function __construct()
    {
        $this->days = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTrip(): Trip
    {
        return $this->trip;
    }

    public function getCountryName(): string
    {
        return $this->countryName;
    }

    public function getCityName(): string
    {
        return $this->cityName;
    }

    public function getCurrency(): string
    {
        return $this->currency ?? $this->trip->getCurrency();
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function getArrivalDate(): \DateTimeImmutable
    {
        return $this->arrivalDate;
    }

    public function getDepartureDate(): \DateTimeImmutable
    {
        return $this->departureDate;
    }

    public function getInterests(): array
    {
        return $this->interests;
    }
    public function getTripStyle(): ?string
    {
        return $this->tripStyle;
    }

    public function getLocalTransport(): ?string
    {
        return $this->localTransport;
    }

    public function getDuration(): int
    {
        return $this->days->count();
    }

    /** @return Collection<int, TripDay> */
    public function getDays(): Collection
    {
        return $this->days;
    }

    public function setDays(Collection $days): void
    {
        $this->days = $days;
    }

    public function setLocalTransport(?string $localTransport): void
    {
        $this->localTransport = $localTransport;
    }

    public function setTripStyle(string $tripStyle): void
    {
        $this->tripStyle = $tripStyle;
    }

    public function setInterests(array $interests): void
    {
        $this->interests = $interests;
    }

    public function setDepartureDate(\DateTimeImmutable $departureDate): void
    {
        $this->departureDate = $departureDate;
    }

    public function setArrivalDate(\DateTimeImmutable $arrivalDate): void
    {
        $this->arrivalDate = $arrivalDate;
    }

    public function setBudget(float $budget): void
    {
        $this->budget = $budget;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setCityName(string $cityName): void
    {
        $this->cityName = $cityName;
    }

    public function setCountryName(string $countryName): void
    {
        $this->countryName = $countryName;
    }

    public function setTrip(Trip $trip): void
    {
        $this->trip = $trip;
    }

    public function addDay(TripDay $day): static
    {
        if (!$this->days->contains($day)) {
            $this->days->add($day);
            $day->setStop($this);
        }

        return $this;
    }

    public function removeDay(TripDay $day): static
    {
        if ($this->days->removeElement($day)) {
            // set the owning side to null (unless already changed)
            if ($day->getStop() === $this) {
                $day->setStop(null);
            }
        }

        return $this;
    }
}
