<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\WeatherApi;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\ApiIntegrations\Utils\PromiseUtils;
use Mazur\Application\AirQuality\ApiIntegrations\Utils\ResponseUtils;
use Mazur\Application\Repository\City\CitiesRepository;

/** @psalm-import-type _Promise from PromiseUtils */
final class WeatherApiIntegration
{
    private const string ACTION_CURRENT = '/current.json';

    public function __construct(
        private readonly Client $httpClient,
        private readonly PromiseUtils $promiseUtils,
        private readonly ResponseUtils $responseUtils,
        private readonly CitiesRepository $citiesRepository
    ) {
    }

    public function getAirQualityForMany(Collection $coordinates, bool $asGenerator = false): Generator
    {
        $promises = [];
        $url = config('weather_api.base_url') . self::ACTION_CURRENT;
        $apiKey = config('weather_api.api_key');
        foreach ($coordinates as $coordinate) {
            $promises[] = $this->httpClient->getAsync($url, [
                'query' => [
                    'q' => $coordinate->latitude . ',' . $coordinate->longitude,
                    'aqi' => 'yes',
                    'key' => $apiKey,
                ],
            ]);
        }

        $fulfilledPromises = Utils::settle($promises)->wait();

        return $this->collectFulfilled($fulfilledPromises);
    }

    private function collectFulfilled(iterable $fulfilledPromises): Generator
    {
        /** @var _Promise $promise */
        foreach ($fulfilledPromises as $promise) {
            if (!$this->promiseUtils->isFulfilled($promise)) {
                continue;
            }

            /** @var Response $response */
            $response = $promise['value'];
            if (!$this->responseUtils->isOk($response)) {
                continue;
            }

            $rawResponse = $response->getBody()->getContents();

            $arr = json_decode($rawResponse, true, 512, JSON_THROW_ON_ERROR);

            $city = $this->citiesRepository->findByCoords(
                (float)$arr['location']['lat'],
                (float)$arr['location']['lon']
            );

            if ($city === null) {
                Log::critical('Unable to find corresponding city for coordinates', [
                    'latitude' => $arr['location']['lat'],
                    'longitude' => $arr['location']['lon'],
                ]);

                $city = $this->citiesRepository->findByCity($arr['location']['name']);
                if ($city === null) {
                    Log::critical('Unable to find corresponding city for name', [
                        'name' => $arr['location']['name'],
                    ]);

                    continue;
                }
            }

            yield new AirQuality(
                cityId: $city->id,
                co: $arr['current']['air_quality']['co'] ?? .0,
                no: $arr['current']['air_quality']['no'] ?? .0,
                no2: $arr['current']['air_quality']['no2'] ?? .0,
                o3: $arr['current']['air_quality']['o3'] ?? .0,
                so2: $arr['current']['air_quality']['so2'] ?? .0,
                pm2_5: $arr['current']['air_quality']['pm2_5'] ?? .0,
                pm10: $arr['current']['air_quality']['pm10'] ?? .0,
                nh3: $arr['current']['air_quality']['nh3'] ?? .0,
            );
        }
    }
}
