<?php

namespace DigitalMarketingFramework\Core\Api\Route;

interface SimpleRouteInterface extends RouteInterface
{
    public function getPath(): string;
}
