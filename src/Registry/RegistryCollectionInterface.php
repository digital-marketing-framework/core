<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Alert\AlertManagerInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface RegistryCollectionInterface extends ContextRegistryInterface
{
    /**
     * @param 'core'|'distributor'|'collector' $domain
     */
    public function addRegistry(string $domain, RegistryInterface $registry): void;

    /**
     * @param 'core'|'distributor'|'collector' $domain
     */
    public function getRegistry(string $domain): RegistryInterface;

    /**
     * @template RegistryClass of RegistryInterface
     *
     * @param class-string<RegistryClass> $class
     *
     * @return RegistryClass
     */
    public function getRegistryByClass(string $class): RegistryInterface;

    /**
     * @return array{core:RegistryInterface,distributor?:RegistryInterface,collector?:RegistryInterface}
     */
    public function getAllRegistries(): array;

    // collect actions //

    public function getConfigurationSchemaDocument(): SchemaDocument;

    public function getGlobalConfigurationSchemaDocument(): SchemaDocument;

    /**
     * @return array<string,array<string,array<string>>>
     */
    public function getFrontendScripts(bool $activeOnly = false): array;

    /**
     * @return array<string,array<string>>
     */
    public function getConfigurationEditorScripts(): array;

    /**
     * @return array{settings:array<string,mixed>,urls:array<string,string>,pluginSettings:array<string,array<string,mixed>>}
     */
    public function getFrontendSettings(): array;

    public function getApiEntryRouteResolver(): EntryRouteResolverInterface;

    public function getNotificationManager(): NotificationManagerInterface;

    public function setNotificationManager(NotificationManagerInterface $notificationManager): void;

    public function getAlertManager(): AlertManagerInterface;

    public function setAlertManager(AlertManagerInterface $alertManager): void;
}
