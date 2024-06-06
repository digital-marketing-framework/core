<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorage;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\CoreRouteResolver;
use DigitalMarketingFramework\Core\Api\RouteResolver\CoreRouteResolverInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;

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

    protected function addPermissionsRouteSettings(array &$settings): void
    {
        $entryRouteResolver = $this->getRegistryCollection()->getApiEntryRouteResolver();
        $coreRouteResolver = $this->getCoreApiRouteResolver();
        $route = $coreRouteResolver->getPermissionsRoute();
        $settings = $coreRouteResolver->getPermissionsFrontendSettings();

        $id = $route->getId();
        $settings['pluginSettings'][$id] = $settings;
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
