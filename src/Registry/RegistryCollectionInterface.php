<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface RegistryCollectionInterface
{
    /**
     * @param 'core'|'distributor'|'collector' $domain
     */
    public function addRegistry(string $domain, RegistryInterface $registry): void;

    /**
     * @param 'core'|'distributor'|'collector' $domain
     */
    public function getRegistry(string $domain): ?RegistryInterface;

    /**
     * @return array{core:RegistryInterface,distributor?:RegistryInterface,collector?:RegistryInterface}
     */
    public function getAllRegistries(): array;

    // collect actions //

    public function getConfigurationSchemaDocument(): SchemaDocument;

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

    public function addApiRouteResolvers(EntryRouteResolverInterface $entryResolver): void;
}
