<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\AqiCalculator;

use Mazur\Application\AirQuality\AqiCalculator\Enums\AqiType;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\AqiCalculator\Exceptions\PollutantNotFoundException;
use Mazur\Application\AirQuality\Entity\IndexStringRepresentation;

final class AqiCalculator
{
    private ?UkCalculator $ukCalculator = null;
    private ?UsCalculator $usCalculator = null;
    private ?EuropeanCalculator $europeanCalculator = null;

    public function getStringRepresentation(int $index, AqiType $aqiType): IndexStringRepresentation
    {
        return match ($aqiType) {
            AqiType::UK => $this->getUkCalculator()->getStringRepresentation($index),
            AqiType::US => $this->getUsCalculator()->getStringRepresentation($index),
            AqiType::EUROPE => $this->getEuropeanCalculator()->getStringRepresentation($index),
        };
    }

    /** @throws PollutantNotFoundException */
    public function calculate(AirQuality $airQuality, AqiType $aqiType): int
    {
        return match ($aqiType) {
            AqiType::UK => $this->getUkCalculator()->calculate($airQuality),
            AqiType::US => $this->getUsCalculator()->calculate($airQuality),
            AqiType::EUROPE => $this->getEuropeanCalculator()->calculate($airQuality),
        };
    }

    private function getUkCalculator(): UkCalculator
    {
        if ($this->ukCalculator === null) {
            $this->ukCalculator = app()->make(UkCalculator::class);
        }

        return $this->ukCalculator;
    }

    private function getUsCalculator(): UsCalculator
    {
        if ($this->usCalculator === null) {
            $this->usCalculator = app()->make(UsCalculator::class);
        }

        return $this->usCalculator;
    }

    private function getEuropeanCalculator(): EuropeanCalculator
    {
        if ($this->europeanCalculator === null) {
            $this->europeanCalculator = app()->make(EuropeanCalculator::class);
        }

        return $this->europeanCalculator;
    }
}
