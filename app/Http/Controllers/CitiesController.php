<?php

declare(strict_types=1);

namespace Mazur\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Mazur\Application\AirQuality\ApiIntegrations\Enum\Provider;
use Mazur\Application\AirQuality\AqiCalculator\AqiCalculator;
use Mazur\Application\AirQuality\AqiCalculator\Enums\AqiType;
use Mazur\Application\AirQuality\Entity\MapAirQuality;
use Mazur\Application\AirQuality\SourceUnion\MaxSelectionSourceUnion;
use Mazur\Application\Repository\AirQuality\AirQualityRepository;
use Mazur\Application\Repository\City\CitiesRepository;
use Mazur\Models\City;

final class CitiesController extends Controller
{
    public function __construct(
        private AirQualityRepository $airQualityRepository,
        private MaxSelectionSourceUnion $maxSelectionSourceUnion,
        private AqiCalculator $aqiCalculator
    ) {
    }

    public function supportedCities(
        Request $request,
        CitiesRepository $citiesRepository,
        ResponseFactory $responseFactory
    ): Response|JsonResponse {
        if ($request->ajax()) {
            return $responseFactory->json($citiesRepository->all(['name'])->chunk(100));
        }

        return $responseFactory->view('cities.supported-cities');
    }

    public function cityDetails(Request $request, City $city, ResponseFactory $responseFactory): Response
    {
        if ($request->ajax()) {
            return $responseFactory->noContent(Response::HTTP_FORBIDDEN);
        }

        /** @var ?MapAirQuality $airQualityResult */
        $airQualityResult = $this->getAirQualityResultsForCity($city->id)->first();
        $aqiUsStr = $this->aqiCalculator->getStringRepresentation($airQualityResult->aqiUs, AqiType::US);
        $aqiUkStr = $this->aqiCalculator->getStringRepresentation($airQualityResult->aqiUk, AqiType::UK);
        $aqiEuStr = $this->aqiCalculator->getStringRepresentation($airQualityResult->aqiEu, AqiType::EUROPE);

        $measurements = [
            [
                'pollutant' => 'PM2.5',
                'value' => $airQualityResult->pm2_5 == 0 ? 'N/A' : $airQualityResult->pm2_5,
                'unit' => 'µg/m³',
            ],
            [
                'pollutant' => 'PM10',
                'value' => $airQualityResult->pm10 == 0 ? 'N/A' : $airQualityResult->pm10,
                'unit' => 'µg/m³',
            ],
            [
                'pollutant' => 'O3',
                'value' => $airQualityResult->o3 == 0 ? 'N/A' : $airQualityResult->o3,
                'unit' => 'µg/m³',
            ],
            [
                'pollutant' => 'NO',
                'value' => $airQualityResult->no == 0 ? 'N/A' : $airQualityResult->no,
                'unit' => 'µg/m³',
            ],
            [
                'pollutant' => 'NO2',
                'value' => $airQualityResult->no2 == 0 ? 'N/A' : $airQualityResult->no2,
                'unit' => 'µg/m³',
            ],
            [
                'pollutant' => 'SO2',
                'value' => $airQualityResult->so2 == 0 ? 'N/A' : $airQualityResult->so2,
                'unit' => 'µg/m³',
            ],
            [
                'pollutant' => 'CO',
                'value' => $airQualityResult->co == 0 ? 'N/A' : $airQualityResult->co,
                'unit' => 'µg/m³',
            ],
            [
                'pollutant' => 'NH3',
                'value' => $airQualityResult->nh3 == 0 ? 'N/A' : $airQualityResult->nh3,
                'unit' => 'µg/m³',
            ],
        ];

        $aqis = $airQualityResult === null
            ? []
            : [
                [
                    'type' => 'US',
                    'value' => $airQualityResult->aqiUs . ' (' . $aqiUsStr->index . ')',
                    'description' => $aqiUsStr->description,
                ],
                [
                    'type' => 'UK',
                    'value' => $airQualityResult->aqiUk . ' (' . $aqiUkStr->index . ')',
                    'description' => $aqiUkStr->description,
                ],
                [
                    'type' => 'EU',
                    'value' => $airQualityResult->aqiEu . ' (' . $aqiEuStr->index . ')',
                    'description' => $aqiEuStr->description,
                ],
            ];

        return $responseFactory->view(
            'cities.details',
            [
                'city' => $city->only('name', 'latitude', 'longitude'),
                'measurements' => $measurements,
                'aqis' => $aqis,
            ]
        );
    }

    private function getAirQualityResultsForCity(int $cityId): Collection
    {
        $openWeatherIndexes = $this->airQualityRepository->getLatestAirQualityIndexForCity(
            $cityId,
            Provider::OPEN_WEATHER
        );
        $weatherApiIndexes = $this->airQualityRepository->getLatestAirQualityIndexForCity(
            $cityId,
            Provider::WEATHER_API
        );
        $unitedIndexes = $this->maxSelectionSourceUnion->uniteRaws($openWeatherIndexes, $weatherApiIndexes);

        foreach ($unitedIndexes as $key => $index) {
            if ($index->aqiUs === null) {
                $aqiUk = $this->aqiCalculator->calculate($index->toAirQuality(), AqiType::UK);
                $aqiUs = $this->aqiCalculator->calculate($index->toAirQuality(), AqiType::US);
                $aqiEu = $this->aqiCalculator->calculate($index->toAirQuality(), AqiType::EUROPE);
                $index = $index->withAqiIndexes($aqiUk, $aqiUs, $aqiEu);
                $unitedIndexes[$key] = $index;
            }
        }

        return $unitedIndexes;
    }
}
