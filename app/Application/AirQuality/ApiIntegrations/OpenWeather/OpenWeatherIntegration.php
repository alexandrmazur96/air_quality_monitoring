<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\OpenWeather;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use JsonException;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\Coordinates;
use Mazur\Application\AirQuality\ApiIntegrations\Utils\PromiseUtils;
use Mazur\Application\AirQuality\ApiIntegrations\Utils\ResponseUtils;
use Mazur\Application\Repository\City\CitiesRepository;

/** @psalm-import-type _Promise from PromiseUtils */
final class OpenWeatherIntegration
{
    private const string ACTION_AIR_POLLUTION = '/air_pollution';

    public function __construct(
        private readonly Client $httpClient,
        private readonly PromiseUtils $promiseUtils,
        private readonly ResponseUtils $responseUtils,
        private readonly CitiesRepository $citiesRepository
    ) {
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getAirQuality(float $latitude, float $longitude): array
    {
        $response = $this->httpClient->get(config('open_weather.base_url') . self::ACTION_AIR_POLLUTION, [
            'query' => [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => config('open_weather.api_key'),
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param Collection<array-key, Coordinates> $coordinates
     * @throws JsonException
     */
    public function getAirQualityForMany(Collection $coordinates, bool $asGenerator = false): Generator|Collection
    {
        $promises = [];
        $url = config('open_weather.base_url') . self::ACTION_AIR_POLLUTION;
        $apiKey = config('open_weather.api_key');
        foreach ($coordinates as $coordinate) {
            $promises[] = $this->httpClient->getAsync($url, [
                'query' => [
                    'lat' => $coordinate->latitude,
                    'lon' => $coordinate->longitude,
                    'appid' => $apiKey,
                ],
            ]);
        }

        $fulfilledPromises = Utils::settle($promises)->wait();

        return $asGenerator
            ? $this->collectFulfilledAsGenerator($fulfilledPromises)
            : $this->collectFulfilledAsCollection($fulfilledPromises);
    }

    /** @throws JsonException */
    private function collectFulfilledAsCollection(iterable $fulfilledPromises): Collection
    {
        $results = new Collection();

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
            $results->push(
                new AirQuality(
                    cityId: $this->citiesRepository->findByCoords($arr['coord']['lat'], $arr['coord']['lon'])->id,
                    co: $arr['list'][0]['components']['co'],
                    no: $arr['list'][0]['components']['no'],
                    no2: $arr['list'][0]['components']['no2'],
                    o3: $arr['list'][0]['components']['o3'],
                    so2: $arr['list'][0]['components']['so2'],
                    pm2_5: $arr['list'][0]['components']['pm2_5'],
                    pm10: $arr['list'][0]['components']['pm10'],
                    nh3: $arr['list'][0]['components']['nh3'],
                )
            );
        }

        return $results;
    }

    private function collectFulfilledAsGenerator(iterable $fulfilledPromises): Generator
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

            $city = $this->citiesRepository->findByCoords((float)$arr['coord']['lat'], (float)$arr['coord']['lon']);
            if ($city === null) {
                Log::critical('Unable to find corresponding city for coordinates', [
                    'latitude' => $arr['coord']['lat'],
                    'longitude' => $arr['coord']['lon'],
                ]);
                continue;
            }

            yield new AirQuality(
                cityId: $city->id,
                co: $arr['list'][0]['components']['co'],
                no: $arr['list'][0]['components']['no'],
                no2: $arr['list'][0]['components']['no2'],
                o3: $arr['list'][0]['components']['o3'],
                so2: $arr['list'][0]['components']['so2'],
                pm2_5: $arr['list'][0]['components']['pm2_5'],
                pm10: $arr['list'][0]['components']['pm10'],
                nh3: $arr['list'][0]['components']['nh3'],
            );
        }
    }
}
