<?php

namespace App\Service;

use App\DTO\TripPlan\DayPlan;
use App\DTO\TripPlan\StopPlan;
use App\DTO\TripPlan\TripPlan;
use App\Entity\Trip;

readonly class TripPlanMapper
{
    public static function fromEntity(Trip $trip): TripPlan
    {
        $dto = new TripPlan();
        $dto->name = $trip->getTitle();
        $dto->currency = $trip->getCurrency();
        $dto->startDate = $trip->getStartDate();
        $dto->endDate = $trip->getEndDate();
        $dto->totalDuration = $trip->getTotalDuration();
        $dto->totalBudget = $trip->getTotalBudget();

        foreach ($trip->getStops() as $stop) {
            $stopDto = new StopPlan();
            $stopDto->cityName = $stop->getCityName();
            $stopDto->countryName = $stop->getCountryName();
            $stopDto->startDate = $stop->getArrivalDate();
            $stopDto->endDate = $stop->getDepartureDate();
            $stopDto->currency = $stop->getCurrency();
            $stopDto->tripStyle = $stop->getTripStyle();
            $stopDto->budget = $stop->getBudget();
            $stopDto->localTransportInfo = $stop->getLocalTransport();
            $stopDto->interests = $stop->getInterests();
            $stopDto->duration = $stop->getDuration();

            foreach ($stop->getDays() as $day) {
                $dayDto = new DayPlan();

                $dayDto->date = $day->getDate();
                $dayDto->activities = $day->getActivities();
                $dayDto->foodPlaces = $day->getFoodPlaces();

                $stopDto->days[] = $dayDto;
            }

            $dto->stops[] = $stopDto;
        }

        return $dto;
    }
}
