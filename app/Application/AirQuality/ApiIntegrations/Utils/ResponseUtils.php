<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\Utils;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class ResponseUtils
{
    public function isOk(Response $response): bool
    {
        return $response->getStatusCode() === SymfonyResponse::HTTP_OK;
    }
}
