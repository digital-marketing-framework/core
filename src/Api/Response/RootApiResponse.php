<?php

namespace DigitalMarketingFramework\Core\Api\Response;

use DigitalMarketingFramework\Core\Api\Route\RouteInterface;

class RootApiResponse implements ApiResponseInterface
{
    /**
     * @param array<RouteInterface> $routes
     */
    public function __construct(
        protected array $routes,
    ) {
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    public function getStatusMessage(): ?string
    {
        return null;
    }

    public function getData(): array
    {
        $result = [];
        foreach ($this->routes as $route) {
            $result['resources'][] = $route->toArray();
        }

        return $result;
    }

    public function getContent(): string
    {
        return json_encode($this->getData(), flags: JSON_THROW_ON_ERROR);
    }
}
