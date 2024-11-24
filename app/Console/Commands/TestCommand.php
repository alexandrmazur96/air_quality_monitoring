<?php

declare(strict_types=1);

namespace Mazur\Console\Commands;

use Illuminate\Console\Command;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\Coordinates;
use Mazur\Application\AirQuality\ApiIntegrations\WeatherApi\WeatherApiIntegration;
use Mazur\Models\City;

final class TestCommand extends Command
{
    protected $signature = 'test-code:run';

    public function handle(): void
    {
        $result = app(WeatherApiIntegration::class)
            ->getAirQualityForMany(
                collect([City::first()])->map(
                    static fn(City $city): Coordinates => new Coordinates(
                        latitude : $city->latitude,
                        longitude: $city->longitude
                    )
                )
            );

        foreach ($result as $res) {
            dd($res);
        }
    }
}
