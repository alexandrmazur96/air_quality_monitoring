<?php

declare(strict_types=1);

namespace Mazur\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mazur\Application\Repository\City\CitiesRepository;

final class AboutController extends Controller
{
    public function about(Request $request, ResponseFactory $responseFactory): Response
    {
        if ($request->ajax()) {
            return $responseFactory->noContent(Response::HTTP_FORBIDDEN);
        }

        return $responseFactory->view('about.index');
    }

    public function supportedCities(
        Request $request,
        CitiesRepository $citiesRepository,
        ResponseFactory $responseFactory
    ): Response|JsonResponse {
        if ($request->ajax()) {
            return $responseFactory->json($citiesRepository->all(['name'])->chunk(100));
        }

        return $responseFactory->view('about.cities');
    }
}
