<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\CoreRouteResolverInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

interface ApiRegistryInterface
{
    /**
     * @return EndPointStorageInterface<EndPointInterface>
     */
    public function getEndPointStorage(): EndPointStorageInterface;

    /**
     * @param EndPointStorageInterface<EndPointInterface> $endPointStorage
     */
    public function setEndPointStorage(EndPointStorageInterface $endPointStorage): void;

    public function getCoreApiRouteResolver(): CoreRouteResolverInterface;

    /**
     * @return array<string,RouteResolverInterface>
     */
    public function getApiRouteResolvers(): array;

    /**
     * @return array{settings:array<string,mixed>,urls:array<string,string>,pluginSettings:array<string,array<string,mixed>>}
     */
    public function getFrontendSettings(): array;
}
