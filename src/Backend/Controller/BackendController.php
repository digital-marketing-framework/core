<?php

namespace DigitalMarketingFramework\Core\Backend\Controller;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Backend\UriBuilderInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class BackendController extends Plugin implements BackendControllerInterface
{
    protected UriBuilderInterface $uriBuilder;

    protected Request $request;

    /**
     * @param array<string> $routes
     */
    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
        protected string $type,
        protected string $section,
        protected array $routes,
    ) {
        parent::__construct($keyword);
        $this->uriBuilder = $registry->getBackendUriBuilder();
    }

    abstract public function getResponse(Request $request): Response;

    public function getType(): string
    {
        return $this->type;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getSupportedRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getParameters(): array
    {
        $params = $this->request->getArguments();

        if ($this->request->getMethod() === 'POST') {
            foreach ($this->request->getData() as $key => $value) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public function matchRequest(Request $request): bool
    {
        if ($this->getType() !== $request->getType()) {
            return false;
        }

        if ($this->getSection() !== $request->getSection()) {
            return false;
        }

        return in_array($request->getInternalRoute(), $this->routes, true);
    }

    protected function getInternalRoute(): string
    {
        $internalRoute = $this->request->getData()['action'] ?? '';
        if (!in_array($internalRoute, $this->getSupportedRoutes(), true)) {
            $internalRoute = $this->request->getInternalRoute();
        }

        return $internalRoute;
    }

    protected function getAction(bool $dashed = false): string
    {
        $internalRoute = $this->getInternalRoute();

        if ($dashed) {
            return $internalRoute;
        }

        return preg_replace_callback('/[-.](.)/', static fn (array $matches): string => strtoupper($matches[1]), $internalRoute);
    }

    protected function callActionMethod(): Response
    {
        $action = $this->getAction();
        $method = $action . 'Action';

        if (!method_exists($this, $method)) {
            throw new DigitalMarketingFrameworkException(sprintf('Unknown action "%s".', $action));
        }

        /** @phpstan-ignore method.dynamicName (intentional action dispatch pattern) */
        $response = $this->$method();

        if (!$response instanceof Response) {
            throw new DigitalMarketingFrameworkException('Backend controller did not return a valid response object.');
        }

        return $response;
    }
}
