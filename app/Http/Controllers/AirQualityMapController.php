<?php

declare(strict_types=1);

namespace Mazur\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
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
        ResponseFactory $responseFactory
    ): SymfonyResponse {
        if (!$request->ajax()) {
            return $responseFactory->noContent(SymfonyResponse::HTTP_FORBIDDEN);
        }

        $currentAirQualityIndexes = $airQualityRepository->getCurrentAirQualityIndexes();

        return $responseFactory->json($currentAirQualityIndexes);
    }
}
