<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolver;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;

trait ApiRegistryTrait
{
    protected EntryRouteResolverInterface $routeResolver;

    public function getApiRouteResolvers(): array
    {
        return [];
    }

    public function getApiEntryRouteResolver(): EntryRouteResolverInterface
    {
        if (!isset($this->routeResolver)) {
            $this->routeResolver = $this->createObject(EntryRouteResolver::class);
            foreach ($this->getApiRouteResolvers() as $domain => $resolver) {
                $this->routeResolver->registerResolver($domain, $resolver);
            }
        }

        return $this->routeResolver;
    }

    public function setApiEntryRouteResolver(EntryRouteResolverInterface $routeResolver): void
    {
        $this->routeResolver = $routeResolver;
    }

    public function registerApiRouteResolver(string $domain, RouteResolverInterface $routeResolver): void
    {
        $this->getApiEntryRouteResolver()->registerResolver($domain, $routeResolver);
    }

    public function getRegisteredApiRouteResolver(string $domain): ?RouteResolverInterface
    {
        return $this->routeResolver->getResolver($domain);
    }
}
