<?php

namespace DigitalMarketingFramework\Core\Api\RouteResolver;

use DigitalMarketingFramework\Core\Api\Route\SimpleRouteInterface;

interface CoreRouteResolverInterface extends RouteResolverInterface
{
    public const SEGMENT_CORE = 'core';

    public const VARIABLE_ACTION = 'action';

    public const ACTION_PERMISSIONS = 'permissions';

    public function getPermissionsRoute(): SimpleRouteInterface;

    /**
     * @return array<string,mixed>
     */
    public function getPermissionsFrontendSettings(): array;
}
