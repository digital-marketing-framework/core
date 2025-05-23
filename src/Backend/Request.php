<?php

namespace DigitalMarketingFramework\Core\Backend;

class Request
{
    protected string $type;

    protected string $section;

    protected string $internalRoute;

    /**
     * @param array<string,mixed> $arguments
     * @param array<string,mixed> $data
     */
    public function __construct(
        protected string $route,
        protected array $arguments = [],
        protected array $data = [],
        protected string $method = 'GET',
    ) {
        $routeParts = $route === '' ? [] : explode('.', $route);
        $this->type = array_shift($routeParts) ?? 'page';
        $this->section = array_shift($routeParts) ?? 'core';
        $this->internalRoute = $routeParts !== [] ? implode('.', $routeParts) : 'index';
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getOriginalRoute(): string
    {
        return $this->route;
    }

    public function getRoute(): string
    {
        return implode('.', [$this->type, $this->section, $this->internalRoute]);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getInternalRoute(): string
    {
        return $this->internalRoute;
    }

    /**
     * @return array<string,mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
