<?php

namespace DigitalMarketingFramework\Core\Api\RouteResolver;

use DigitalMarketingFramework\Core\Api\Request\ApiRequestInterface;

interface EntryRouteResolverInterface extends RouteResolverInterface
{
    public const SEGMENT_ROOT = 'root';

    public function enabled(): bool;

    public function getBasePath(): string;

    public function getFullPath(string $path): string;

    /**
     * @param ?array<string,mixed> $data
     */
    public function buildRequest(string $route, string $method = 'GET', ?array $data = null): ApiRequestInterface;

    public function registerResolver(string $domain, RouteResolverInterface $resolver): void;

    public function getResolver(string $domain): ?RouteResolverInterface;
}
