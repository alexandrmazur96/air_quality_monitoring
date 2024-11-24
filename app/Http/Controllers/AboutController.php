<?php

declare(strict_types=1);

namespace Mazur\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AboutController extends Controller
{
    public function about(Request $request, ResponseFactory $responseFactory): Response
    {
        if ($request->ajax()) {
            return $responseFactory->noContent(Response::HTTP_FORBIDDEN);
        }

        return $responseFactory->view('about.index');
    }

    public function aqiUs(Request $request, ResponseFactory $responseFactory): Response
    {
        if ($request->ajax()) {
            return $responseFactory->noContent(Response::HTTP_FORBIDDEN);
        }

        return $responseFactory->view('about.aqi_us');
    }

    public function aqiUk(Request $request, ResponseFactory $responseFactory): Response
    {
        if ($request->ajax()) {
            return $responseFactory->noContent(Response::HTTP_FORBIDDEN);
        }

        return $responseFactory->view('about.aqi_uk');
    }

    public function aqiEu(Request $request, ResponseFactory $responseFactory): Response
    {
        if ($request->ajax()) {
            return $responseFactory->noContent(Response::HTTP_FORBIDDEN);
        }

        return $responseFactory->view('about.aqi_eu');
    }
}
