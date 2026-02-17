<?php

namespace DigitalMarketingFramework\Core\Backend\UriRouteResolver;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface UriRouteResolverInterface extends PluginInterface
{
    /**
     * @param array<string,mixed> $arguments
     */
    public function resolve(string $route, array $arguments = []): ?string;
}
