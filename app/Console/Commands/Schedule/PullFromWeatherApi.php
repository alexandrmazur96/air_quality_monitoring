<?php

declare(strict_types=1);

namespace Mazur\Console\Commands\Schedule;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\Coordinates;
use Mazur\Application\AirQuality\ApiIntegrations\Enum\Provider;
use Mazur\Application\AirQuality\ApiIntegrations\WeatherApi\WeatherApiIntegration;
use Mazur\Application\Repository\AirQuality\AirQualityRepository;
use Mazur\Application\Repository\City\CitiesRepository;
use Mazur\Models\City;

final class PullFromWeatherApi extends Command
{
    private const int CHUNK_SIZE = 25;

    protected $signature = 'air-quality:pull:weather-api';
    protected $description = 'Pull air quality data from Weather API';

    public function handle(
        WeatherApiIntegration $weatherApiIntegration,
        CitiesRepository $citiesRepository,
        AirQualityRepository $airQualityRepository
    ): void {
        $this->info('Pulling air quality data from WeatherAPI');

        $cities = $citiesRepository->all();
        $bar = $this->output->createProgressBar($cities->count());

        $bar->start();
        DB::beginTransaction();
        $airQualityRepository->markCurrentRecordsAsNonLatest(Provider::WEATHER_API);
        /** @var Collection<array-key, City> $citiesChunk */
        foreach ($cities->chunk(self::CHUNK_SIZE) as $citiesChunk) {
            $resultsForChunk = $weatherApiIntegration->getAirQualityForMany(
                $citiesChunk->map(
                    static fn(City $city): Coordinates => new Coordinates(
                        latitude : $city->latitude,
                        longitude: $city->longitude
                    )
                ),
                true
            );
            $airQualityRepository->create(Provider::WEATHER_API, $resultsForChunk);

            $bar->advance(self::CHUNK_SIZE);
        }
        DB::commit();
        $bar->finish();
        $this->info('');

        $this->info('Air quality data from Weather API has been pulled');
    }
}
