<?php

declare(strict_types=1);

namespace Mazur\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
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
}
