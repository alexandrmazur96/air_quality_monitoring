<?php

declare(strict_types=1);

namespace Mazur\Console\Commands\Schedule;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\Coordinates;
use Mazur\Application\AirQuality\ApiIntegrations\OpenWeather\OpenWeatherIntegration;
use Mazur\Application\Repository\AirQuality\AirQualityRepository;
use Mazur\Application\Repository\City\CitiesRepository;
use Mazur\Models\City;

final class PullFromOpenWeather extends Command
{
    private const int CHUNK_SIZE = 25;

    protected $signature = 'air-quality:pull:open-weather';
    protected $description = 'Pull air quality data from OpenWeather';

    public function handle(
        OpenWeatherIntegration $openWeatherIntegration,
        CitiesRepository $citiesRepository,
        AirQualityRepository $airQualityRepository
    ): void {
        $this->info('Pulling air quality data from OpenWeather');

        $cities = $citiesRepository->all();
        $bar = $this->output->createProgressBar($cities->count());

        $bar->start();
        /** @var Collection<array-key, City> $citiesChunk */
        foreach ($cities->chunk(self::CHUNK_SIZE) as $citiesChunk) {
            $resultsForChunk = $openWeatherIntegration->getAirQualityForMany($citiesChunk->map(
                static fn (City $city): Coordinates => new Coordinates(
                    latitude: $city->latitude,
                    longitude: $city->longitude
                )
            ));

            dd($resultsForChunk);
            $airQualityRepository->create($resultsForChunk);

            $bar->advance(self::CHUNK_SIZE);
        }
        $bar->finish();

        $this->info('Air quality data from OpenWeather has been pulled');
    }
}