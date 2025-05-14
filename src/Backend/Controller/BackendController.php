<?php

namespace DigitalMarketingFramework\Core\Backend\Controller;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class BackendController extends Plugin
{
    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
        protected string $type,
        protected string $section,
        protected array $routes,
    ) {
        parent::__construct($keyword);
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

    public function matchRequest(Request $request): bool
    {
        if ($this->getType() !== $request->getType()) {
            return false;
        }

        if ($this->getSection() !== $request->getSection()) {
            return false;
        }

        if (!in_array($request->getInternalRoute(), $this->routes)) {
            return false;
        }

        return true;
    }

    protected function callActionMethod(Request $request): Response
    {
        $action = preg_replace_callback('/[-.](.)/', function(array $matches) {
            return strtoupper($matches[1]);
        }, $request->getInternalRoute());
        $method = $action . 'Action';

        if (!method_exists($this, $method)) {
            throw new DigitalMarketingFrameworkException(sprintf('Unknown action "%s".', $action));
        }

        $response = $this->$method($request);

        if (!$response instanceof Response) {
            throw new DigitalMarketingFrameworkException('Backend controller did not return a valid response object.');
        }

        return $response;
    }
}
