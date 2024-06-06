<?php

namespace DigitalMarketingFramework\Core\Api\RouteResolver;

use DigitalMarketingFramework\Core\Api\ApiException;
use DigitalMarketingFramework\Core\Api\Request\ApiRequestInterface;
use DigitalMarketingFramework\Core\Api\Response\ApiResponse;
use DigitalMarketingFramework\Core\Api\Response\ApiResponseInterface;
use DigitalMarketingFramework\Core\Api\Route\SimpleRoute;
use DigitalMarketingFramework\Core\Api\Route\SimpleRouteInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class CoreRouteResolver implements CoreRouteResolverInterface, DataPrivacyManagerAwareInterface
{
    use DataPrivacyManagerAwareTrait;

    public function __construct(
        protected RegistryInterface $registry
    ) {
    }

    protected function getPermissionsResponse(): ApiResponseInterface
    {
        $permissions = $this->dataPrivacyManager->getAllPossiblePermissions();
        $granted = $this->dataPrivacyManager->getGrantedPermissions();
        $denied = $this->dataPrivacyManager->getAllDeniedPermissions();

        return new ApiResponse([
            'all' => $permissions,
            'granted' => $granted,
            'denied' => $denied,
        ]);
    }

    public function resolveRequest(ApiRequestInterface $request): ApiResponseInterface
    {
        $action = $request->getVariable(static::VARIABLE_ACTION);

        return match ($action) {
            static::ACTION_PERMISSIONS => $this->getPermissionsResponse(),
            default => throw new ApiException(sprintf('Core API action "%s" unknown.', $action)),
        };
    }

    public function getAllRoutes(): array
    {
        return $this->getAllResourceRoutes();
    }

    public function getAllResourceRoutes(): array
    {
        return [
            $this->getPermissionsRoute(),
        ];
    }

    public function getPermissionsRoute(): SimpleRouteInterface
    {
        return new SimpleRoute(
            id: implode(':', [static::SEGMENT_CORE, static::ACTION_PERMISSIONS]),
            path: implode('/', [static::ACTION_PERMISSIONS]),
            constants: [
                static::VARIABLE_DOMAIN => static::SEGMENT_CORE,
                static::VARIABLE_ACTION => static::ACTION_PERMISSIONS,
            ],
            methods: ['GET']
        );
    }

    public function getPermissionsFrontendSettings(): array
    {
        return $this->dataPrivacyManager->getFrontendSettings();
    }
}
