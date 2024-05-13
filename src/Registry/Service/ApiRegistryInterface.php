<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;

interface ApiRegistryInterface
{
    /**
     * @return array<string,RouteResolverInterface>
     */
    public function getApiRouteResolvers(): array;

    public function getApiEntryRouteResolver(): EntryRouteResolverInterface;

    public function setApiEntryRouteResolver(EntryRouteResolverInterface $routeResolver): void;

    public function registerApiRouteResolver(string $domain, RouteResolverInterface $routeResolver): void;

    public function getRegisteredApiRouteResolver(string $domain): ?RouteResolverInterface;
}
