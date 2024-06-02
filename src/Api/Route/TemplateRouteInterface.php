<?php

namespace DigitalMarketingFramework\Core\Api\Route;

interface TemplateRouteInterface extends RouteInterface
{
    public function getTemplate(): string;

    /**
     * @return array<string,string>
     */
    public function getVariables(): array;

    /**
     * @param array<string,string> $variables
     */
    public function getResourceRoute(string $idAffix, array $variables): SimpleRouteInterface;
}
