<?php

namespace App\Service;

use App\DTO\TripPlan\TripPlan;
use App\Entity\Trip;
use App\Entity\TripDay;
use App\Entity\TripStop;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

readonly class TripPersister
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function persistFromPlan(TripPlan $plan, User $user): Trip
    {
        $trip = new Trip();
        $trip->setUser($user);
        $trip->setTitle($plan->name);
        $trip->setStartDate($plan->startDate);
        $trip->setEndDate($plan->endDate);
        $trip->setCurrency($plan->currency);

        foreach ($plan->stops as $stopIndex => $stopDto) {
            $tripStop = new TripStop();
            $tripStop->setTrip($trip);
            $tripStop->setCityName($stopDto->cityName);
            $tripStop->setCountryName($stopDto->countryName);
            $tripStop->setCurrency($stopDto->currency);
            $tripStop->setBudget($stopDto->budget);
            $tripStop->setTripStyle($stopDto->tripStyle);
            $tripStop->setArrivalDate($stopDto->startDate);
            $tripStop->setDepartureDate($stopDto->endDate);
            $tripStop->setInterests($stopDto->interests);
            $tripStop->setLocalTransport($stopDto->localTransportInfo);

            foreach ($stopDto->days as $dayIndex => $dayDto) {
                $tripDay = new TripDay();
                $tripDay->setStop($tripStop);
                $tripDay->setDayIndex($dayIndex + 1);
                $tripDay->setActivities($dayDto->activities);
                $tripDay->setFoodPlaces($dayDto->foodPlaces);
                $tripDay->setDate($dayDto->date);

                $this->em->persist($tripDay);
            }

            $this->em->persist($tripStop);
        }

        $this->em->persist($trip);
        $this->em->flush();

        return $trip;
    }
}
