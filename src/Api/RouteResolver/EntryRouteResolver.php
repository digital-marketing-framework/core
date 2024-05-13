<?php

namespace DigitalMarketingFramework\Core\Api\RouteResolver;

use DigitalMarketingFramework\Core\Api\ApiException;
use DigitalMarketingFramework\Core\Api\Request\ApiRequest;
use DigitalMarketingFramework\Core\Api\Request\ApiRequestInterface;
use DigitalMarketingFramework\Core\Api\Response\ApiResponse;
use DigitalMarketingFramework\Core\Api\Response\ApiResponseInterface;
use DigitalMarketingFramework\Core\Api\Response\RootApiResponse;
use DigitalMarketingFramework\Core\Api\Route\SimpleRoute;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;

class EntryRouteResolver implements EntryRouteResolverInterface, GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    public const KEY_CONTEXT = 'context';

    public const KEY_PAYLOAD = 'payload';

    /** @var array<string,RouteResolverInterface> */
    protected array $resolvers = [];

    public function enabled(): bool
    {
        return (bool)($this->globalConfiguration->get('core')['api']['enabled'] ?? false);
    }

    public function getBasePath(): string
    {
        return $this->globalConfiguration->get('core')['api']['basePath'] ?? 'digital-marketing-framework/api';
    }

    public function getFullPath(string $path): string
    {
        return '/' . $this->getBasePath() . '/' . trim($path, '/');
    }

    public function buildRequest(string $route, string $method = 'GET', ?array $data = null): ApiRequestInterface
    {
        $context = $data[static::KEY_CONTEXT] ?? null;
        $payload = $data[static::KEY_PAYLOAD] ?? null;

        return new ApiRequest($route, $method, $payload, $context);
    }

    /**
     * @return array<SimpleRoute>
     */
    protected function getRootRoutes(): array
    {
        return [new SimpleRoute(
            id: 'root',
            path: '',
            constants: [
                'domain' => 'root',
            ],
            methods: ['GET']
        )];
    }

    public function getAllRoutes(): array
    {
        $routes = $this->getRootRoutes();
        foreach ($this->resolvers as $resolver) {
            foreach ($resolver->getAllRoutes() as $route) {
                $routes[$route->getId()] = $route;
            }
        }

        return $routes;
    }

    public function getAllResourceRoutes(): array
    {
        $routes = $this->getRootRoutes();
        foreach ($this->resolvers as $resolver) {
            foreach ($resolver->getAllResourceRoutes() as $route) {
                $routes[] = $route;
            }
        }

        return $routes;
    }

    public function registerResolver(string $domain, RouteResolverInterface $resolver): void
    {
        $this->resolvers[$domain] = $resolver;
    }

    public function getResolver(string $domain): ?RouteResolverInterface
    {
        return $this->resolvers[$domain] ?? null;
    }

    public function getRootResponse(): ApiResponseInterface
    {
        return new RootApiResponse($this->getAllRoutes());
        // return new RootApiResponse($this->getAllResourceRoutes());
    }

    protected function resolveRoute(ApiRequestInterface $request): void
    {
        $routes = $this->getAllRoutes();
        foreach ($routes as $route) {
            $variables = $route->matchPath($request->getPath());
            if ($variables !== false) {
                if (!in_array($request->getMethod(), $route->getMethods(), true)) {
                    throw new ApiException(sprintf('Method "%s" not supported by this route.', $request->getMethod()));
                }

                $request->addVariables($variables);

                return;
            }
        }

        throw new ApiException(sprintf('Path "%s" is unknown', $request->getPath()));
    }

    public function resolveRequest(ApiRequestInterface $request): ApiResponseInterface
    {
        try {
            $this->resolveRoute($request);
            $domain = $request->getVariable('domain');
            if ($domain === 'root') {
                return $this->getRootResponse();
            }

            if (!isset($this->resolvers[$domain])) {
                throw new ApiException(sprintf('No route resolver for domain "%s" found.', $domain));
            }

            return $this->resolvers[$domain]->resolveRequest($request);
        } catch (ApiException $e) {
            return new ApiResponse(
                statusCode: $e->getCode(),
                message: $e->getMessage(),
            );
        }
    }
}
