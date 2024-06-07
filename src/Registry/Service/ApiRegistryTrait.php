<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorage;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\CoreRouteResolver;
use DigitalMarketingFramework\Core\Api\RouteResolver\CoreRouteResolverInterface;

trait ApiRegistryTrait
{
    protected EndPointStorageInterface $endPointStorage;

    protected CoreRouteResolverInterface $coreRouteResolver;

    public function getCoreApiRouteResolver(): CoreRouteResolverInterface
    {
        if (!isset($this->coreRouteResolver)) {
            $this->coreRouteResolver = $this->createObject(CoreRouteResolver::class, [$this]);
        }

        return $this->coreRouteResolver;
    }

    public function getEndPointStorage(): EndPointStorageInterface
    {
        if (!isset($this->endPointStorage)) {
            $this->endPointStorage = $this->createObject(EndPointStorage::class);
        }

        return $this->endPointStorage;
    }

    public function setEndPointStorage(EndPointStorageInterface $endPointStorage): void
    {
        $this->endPointStorage = $endPointStorage;
    }

    public function getApiRouteResolvers(): array
    {
        return [
            'core' => $this->getCoreApiRouteResolver(),
        ];
    }

    /**
     * @param array{settings:array<string,mixed>,urls:array<string,string>,pluginSettings:array<string,array<string,mixed>>} $settings
     */
    protected function addPermissionsRouteSettings(array &$settings): void
    {
        $entryRouteResolver = $this->getRegistryCollection()->getApiEntryRouteResolver();
        $coreRouteResolver = $this->getCoreApiRouteResolver();
        $route = $coreRouteResolver->getPermissionsRoute();
        $permissionSettings = $coreRouteResolver->getPermissionsFrontendSettings();

        $id = $route->getId();
        $settings['pluginSettings'][$id] = $permissionSettings;
        $settings['urls'][$id] = $entryRouteResolver->getFullPath($route->getPath());
    }

    public function getFrontendSettings(): array
    {
        $settings = [
            'settings' => [
                'prefix' => 'dmf', // TODO make the markup data attribute name prefix configurable
            ],
            'urls' => [],
            'pluginSettings' => [],
        ];

        $this->addPermissionsRouteSettings($settings);

        return $settings;
    }
}
