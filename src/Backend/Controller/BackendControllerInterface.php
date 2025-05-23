<?php

namespace DigitalMarketingFramework\Core\Backend\Controller;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface BackendControllerInterface extends PluginInterface
{
    public function getResponse(Request $request): Response;

    public function getType(): string;

    public function getSection(): string;

    /**
     * @return array<string>
     */
    public function getSupportedRoutes(): array;

    public function matchRequest(Request $request): bool;
}
