<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\Utils;

use GuzzleHttp\Psr7\Response;

/**
 * @psalm-type _Promise=array{
 *     state: string,
 *     value: Response
 * }
 */
final class PromiseUtils
{
    /** @param _Promise $promise */
    public function isFulfilled(array $promise): bool
    {
        return $promise['state'] === 'fulfilled';
    }
}
