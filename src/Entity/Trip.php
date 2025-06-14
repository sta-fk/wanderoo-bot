<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Trip
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $endDate;

    #[ORM\Column(length: 4)]
    private string $currency = 'USD';

    /**
     * @var Collection<int, TripStop>
     */
    #[ORM\OneToMany(targetEntity: TripStop::class, mappedBy: 'trip')]
    private Collection $stops;

    public function __construct()
    {
        $this->stops = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /** @return Collection<int, TripStop> */
    public function getStops(): Collection
    {
        return $this->stops;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setStartDate(\DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setStops(Collection $stops): void
    {
        $this->stops = $stops;
    }

    public function addTripStop(TripStop $tripStop): static
    {
        if (!$this->stops->contains($tripStop)) {
            $this->stops->add($tripStop);
            $tripStop->setTrip($this);
        }

        return $this;
    }

    public function removeTripStop(TripStop $tripStop): static
    {
        if ($this->stops->removeElement($tripStop)) {
            // set the owning side to null (unless already changed)
            if ($tripStop->getTrip() === $this) {
                $tripStop->setTrip(null);
            }
        }

        return $this;
    }
}
