<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\OpenWeather;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use JsonException;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\Coordinates;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @psalm-type _Promise=array{
 *     state: string,
 *     value: Response
 * }
 */
final class OpenWeatherIntegration
{
    private const string ACTION_AIR_POLLUTION = '/air_pollution';

    public function __construct(private readonly Client $httpClient)
    {
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
            if (!$this->isFulfilled($promise)) {
                continue;
            }

            /** @var Response $response */
            $response = $promise['value'];
            if (!$this->isOk($response)) {
                continue;
            }

            $rawResponse = $response->getBody()->getContents();
            $results->push(json_decode($rawResponse, true, 512, JSON_THROW_ON_ERROR));
        }

        return $results;
    }

    private function collectFulfilledAsGenerator(iterable $fulfilledPromises): Generator
    {
        /** @var _Promise $promise */
        foreach ($fulfilledPromises as $promise) {
            if (!$this->isFulfilled($promise)) {
                continue;
            }

            /** @var Response $response */
            $response = $promise['value'];
            if (!$this->isOk($response)) {
                continue;
            }

            $rawResponse = $response->getBody()->getContents();

            yield json_decode($rawResponse, true, 512, JSON_THROW_ON_ERROR);
        }
    }

    /** @param _Promise $promise */
    private function isFulfilled(array $promise): bool
    {
        return $promise['state'] === 'fulfilled';
    }

    private function isOk(Response $response): bool
    {
        return $response->getStatusCode() === SymfonyResponse::HTTP_OK;
    }
}
