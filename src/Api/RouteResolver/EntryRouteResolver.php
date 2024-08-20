<?php

namespace DigitalMarketingFramework\Core\Api\RouteResolver;

use DigitalMarketingFramework\Core\Api\ApiException;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareTrait;
use DigitalMarketingFramework\Core\Api\Request\ApiRequest;
use DigitalMarketingFramework\Core\Api\Request\ApiRequestInterface;
use DigitalMarketingFramework\Core\Api\Response\ApiResponse;
use DigitalMarketingFramework\Core\Api\Response\ApiResponseInterface;
use DigitalMarketingFramework\Core\Api\Response\RootApiResponse;
use DigitalMarketingFramework\Core\Api\Route\SimpleRoute;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class EntryRouteResolver implements EntryRouteResolverInterface, GlobalConfigurationAwareInterface, EndPointStorageAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use EndPointStorageAwareTrait;

    public const API_VERSION = '1';

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
        return '/' . $this->getBasePath() . '/v' . static::API_VERSION . '/' . trim($path, '/');
    }

    public function buildRequest(string $route, string $method = 'GET', array $arguments = [], ?array $data = null): ApiRequestInterface
    {
        $context = $data[static::KEY_CONTEXT] ?? null;
        $payload = $data[static::KEY_PAYLOAD] ?? null;

        return new ApiRequest($route, $method, $arguments, $payload, $context);
    }

    /**
     * @return array<SimpleRoute>
     */
    protected function getRootRoutes(): array
    {
        return [
            new SimpleRoute(
                id: static::SEGMENT_ROOT,
                path: '',
                constants: [
                    static::VARIABLE_DOMAIN => static::SEGMENT_ROOT,
                ],
                methods: ['GET']
            ),
        ];
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
        // return new RootApiResponse($this->getAllRoutes());
        return new RootApiResponse($this->getAllResourceRoutes());
    }

    protected function processApiVersion(string $path, ApiRequestInterface $request): string
    {
        $path = trim($path, '/');
        $segments = GeneralUtility::castValueToArray($path, '/');
        if ($segments === []) {
            throw new ApiException('No API version provided', 404);
        }

        $versionSegment = array_shift($segments);
        if (!str_starts_with((string)$versionSegment, 'v')) {
            throw new ApiException('No API version provided', 404);
        }

        if ($versionSegment !== 'v' . static::API_VERSION) {
            throw new ApiException(sprintf('API version "%s" not installed.', $versionSegment), 400);
        }

        $request->setApiVersion(static::API_VERSION);

        return implode('/', $segments);
    }

    protected function resolveRoute(ApiRequestInterface $request): void
    {
        $path = $this->processApiVersion($request->getPath(), $request);
        $routes = $this->getAllRoutes();
        foreach ($routes as $route) {
            $variables = $route->matchPath($path);
            if ($variables !== false) {
                if (!in_array($request->getMethod(), $route->getMethods(), true)) {
                    throw new ApiException(sprintf('Method "%s" not supported by this route.', $request->getMethod()));
                }

                $request->addVariables($variables);

                $endPointSegment = $request->getVariable(static::VARIABLE_END_POINT);
                if ($endPointSegment !== null) {
                    $endPointName = GeneralUtility::dashedToCamelCase($endPointSegment);
                    $endPoint = $this->endPointStorage->getEndPointByName($endPointName);

                    if (!$endPoint instanceof EndPointInterface || !$endPoint->getEnabled()) {
                        throw new ApiException('End point not found or disabled', 404);
                    }

                    $request->setEndPoint($endPoint);
                }

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
