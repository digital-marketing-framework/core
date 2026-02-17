<?php

namespace DigitalMarketingFramework\Core\Backend\UriRouteResolver;

use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class UriRouteResolver extends Plugin implements UriRouteResolverInterface
{
    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
    ) {
        parent::__construct($keyword);
    }

    protected function getRouteMatch(): string
    {
        return '*';
    }

    /**
     * @param array<string,mixed> $arguments
     */
    protected function getReturnUrl(array $arguments): string
    {
        return (string)($arguments['returnUrl'] ?? '');
    }

    /**
     * @param array<string,mixed> $arguments
     */
    protected function match(string $route, array $arguments = []): bool
    {
        $routeMatch = $this->getRouteMatch();

        return $routeMatch === '*' || $routeMatch === $route;
    }

    /**
     * @param array<string,mixed> $arguments
     */
    abstract protected function doResolve(string $route, array $arguments = []): ?string;

    public function resolve(string $route, array $arguments = []): ?string
    {
        if (!$this->match($route, $arguments)) {
            return null;
        }

        return $this->doResolve($route, $arguments);
    }
}
