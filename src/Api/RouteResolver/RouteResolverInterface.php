<?php

namespace DigitalMarketingFramework\Core\Api\RouteResolver;

use DigitalMarketingFramework\Core\Api\Request\ApiRequestInterface;
use DigitalMarketingFramework\Core\Api\Response\ApiResponseInterface;
use DigitalMarketingFramework\Core\Api\Route\RouteInterface;
use DigitalMarketingFramework\Core\Api\Route\SimpleRouteInterface;

interface RouteResolverInterface
{
    public const VARIABLE_DOMAIN = 'domain';

    public const VARIABLE_END_POINT = 'end_point';

    public function resolveRequest(ApiRequestInterface $request): ApiResponseInterface;

    /**
     * @return array<RouteInterface>
     */
    public function getAllRoutes(): array;

    /**
     * @return array<SimpleRouteInterface>
     */
    public function getAllResourceRoutes(): array;
}
