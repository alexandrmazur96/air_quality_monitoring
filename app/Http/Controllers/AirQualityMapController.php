<?php

declare(strict_types=1);

namespace Mazur\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Mazur\Application\AirQuality\ApiIntegrations\Enum\Provider;
use Mazur\Application\AirQuality\AqiCalculator\AqiCalculator;
use Mazur\Application\AirQuality\AqiCalculator\Enums\AqiType;
use Mazur\Application\AirQuality\SourceUnion\MaxSelectionSourceUnion;
use Mazur\Application\Repository\AirQuality\AirQualityRepository;
use Mazur\Http\Requests\GetCurrentAirQualityIndexesRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class AirQualityMapController extends Controller
{
    public function index(Request $request, ResponseFactory $responseFactory): SymfonyResponse
    {
        if ($request->ajax()) {
            return $responseFactory->noContent(SymfonyResponse::HTTP_FORBIDDEN);
        }

        return $responseFactory->view('air-quality-map.index');
    }

    public function getCurrentAirQualityIndexes(
        GetCurrentAirQualityIndexesRequest $request,
        AirQualityRepository $airQualityRepository,
        MaxSelectionSourceUnion $maxSelectionSourceUnion,
        AqiCalculator $aqiCalculator,
        ResponseFactory $responseFactory
    ): SymfonyResponse {
        if (!$request->ajax()) {
            return $responseFactory->noContent(SymfonyResponse::HTTP_FORBIDDEN);
        }

        $openWeatherIndexes = $airQualityRepository->getCurrentAirQualityIndexes(Provider::OPEN_WEATHER);
        $weatherApiIndexes = $airQualityRepository->getCurrentAirQualityIndexes(Provider::WEATHER_API);
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

        return $responseFactory->json($unitedIndexes);
    }
}
