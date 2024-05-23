<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;

interface ApiRegistryInterface
{
    public function getEndPointStorage(): EndPointStorageInterface;

    public function setEndPointStorage(EndPointStorageInterface $endPointStorage): void;

    /**
     * @return array<string,RouteResolverInterface>
     */
    public function getApiRouteResolvers(): array;

    public function getApiEntryRouteResolver(): EntryRouteResolverInterface;

    public function setApiEntryRouteResolver(EntryRouteResolverInterface $routeResolver): void;

    public function registerApiRouteResolver(string $domain, RouteResolverInterface $routeResolver): void;

    public function getRegisteredApiRouteResolver(string $domain): ?RouteResolverInterface;

    /**
     * @return array{settings:array<string,mixed>,urls:array<string,string>,pluginSettings:array<string,array<string,mixed>>}
     */
    public function getFrontendSettings(): array;
}