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
}
