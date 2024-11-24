<?php

declare(strict_types=1);

namespace Mazur\Console\Commands\Schedule\Telegram;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Mazur\Application\AirQuality\ApiIntegrations\Enum\Provider;
use Mazur\Application\AirQuality\AqiCalculator\AqiCalculator;
use Mazur\Application\AirQuality\AqiCalculator\Enums\AqiType;
use Mazur\Application\AirQuality\SourceUnion\MaxSelectionSourceUnion;
use Mazur\Application\Repository\AirQuality\AirQualityRepository;
use Mazur\Application\Repository\User\UsersLocationRepository;
use Telegram\Bot\Api;

final class NotifyAirQualityChanges extends Command
{
    protected $signature = 'notify:telegram:air-quality-changes:all';
    protected $description = 'Notify subscribed users about all air quality changes';

    /** @var array<int, Collection> */
    private array $airQualityRecords = [];

    public function handle(UsersLocationRepository $usersLocationRepository, AqiCalculator $aqiCalculator): void {
        $this->info('Notifying all subscribed users about air quality changes');

        $tg = new Api();

        foreach ($usersLocationRepository->getAllSubscribed(with: ['nearestCity']) as $userLocation) {
            if ($this->airQualityRecords[$userLocation->nearestCity->id] === null) {
                $this->airQualityRecords[$userLocation->nearestCity->id] = $this->getAirQualityResultsForCity($userLocation->nearestCity->id);
            }

            $airQualityRecords = $this->airQualityRecords[$userLocation->nearestCity->id];
            if ($airQualityRecords->isEmpty()) {
                continue;
            }

            $indexStrRepresentation = $aqiCalculator->getStringRepresentation(
                $airQualityRecords->first()->aqiUs,
                AqiType::US
            );

            $tg->sendMessage([
                'chat_id' => $userLocation->chat_id,
                'text' => sprintf(
                    'According to US Air Quality the air quality in your area is %s. %s',
                    strtolower($indexStrRepresentation->index),
                    $indexStrRepresentation->description
                ),
            ]);

            $tg->sendMessage([
                'chat_id' => $userLocation->chat_id,
                'text' => 'Details: https://air-quality-ua.com/city/' . $userLocation->nearestCity->id,
            ]);
        }
    }

    private function getAirQualityResultsForCity(int $cityId): Collection
    {
        $airQualityRepository = app(AirQualityRepository::class);
        $maxSelectionSourceUnion = app(MaxSelectionSourceUnion::class);
        $aqiCalculator = app(AqiCalculator::class);

        $openWeatherIndexes = $airQualityRepository->getLatestAirQualityIndexForCity(
            $cityId,
            Provider::OPEN_WEATHER
        );
        $weatherApiIndexes = $airQualityRepository->getLatestAirQualityIndexForCity(
            $cityId,
            Provider::WEATHER_API
        );
        $unitedIndexes = $maxSelectionSourceUnion->uniteRaws($openWeatherIndexes, $weatherApiIndexes);

        foreach ($unitedIndexes as $key => $index) {
            if ($index->aqiUs === null) {
                $aqiUk = $aqiCalculator->calculate($index->toAirQuality(), AqiType::UK);
                $aqiUs = $aqiCalculator->calculate($index->toAirQuality(), AqiType::US);
                $aqiEu = $aqiCalculator->calculate($index->toAirQuality(), AqiType::EUROPE);
                $index = $index->withAqiIndexes($aqiUk, $aqiUs, $aqiEu);
                $unitedIndexes[$key] = $index;
            }
        }

        return $unitedIndexes;
    }
}
