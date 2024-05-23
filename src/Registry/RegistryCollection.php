<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

class RegistryCollection implements RegistryCollectionInterface
{
    /**
     * @param array{core?:RegistryInterface,distributor?:RegistryInterface,collector?:RegistryInterface} $collection
     */
    public function __construct(
        protected array $collection = [],
    ) {
    }

    public function addRegistry(string $domain, RegistryInterface $registry): void
    {
        $this->collection[$domain] = $registry;
    }

    public function getRegistry(string $domain): ?RegistryInterface
    {
        return $this->collection[$domain] ?? null;
    }

    public function getAllRegistries(): array
    {
        if (!isset($this->collection[RegistryDomain::CORE])) {
            throw new DigitalMarketingFrameworkException('Registry collection must have at least the core registry added!');
        }

        return $this->collection;
    }

    public function getConfigurationSchemaDocument(): SchemaDocument
    {
        $document = new SchemaDocument();
        foreach ($this->collection as $registry) {
            $registry->addConfigurationSchemaDocument($document);
        }

        return $document;
    }

    public function getFrontendScripts(bool $activeOnly = false): array
    {
        $frontendScripts = [];
        foreach ($this->collection as $registry) {
            foreach ($registry->getFrontendScripts($activeOnly) as $type => $typeScripts) {
                foreach ($typeScripts as $package => $paths) {
                    $scripts = $frontendScripts[$type][$package] ??= [];
                    array_push($scripts, ...$paths);
                    $frontendScripts[$type][$package] = array_unique($scripts);
                }
            }
        }

        return $frontendScripts;
    }

    public function getConfigurationEditorScripts(): array
    {
        $configurationEditorScripts = [];
        foreach ($this->collection as $registry) {
            foreach ($registry->getConfigurationEditorScripts() as $package => $paths) {
                $scripts = $configurationEditorScripts[$package] ?? [];
                array_push($scripts, ...$paths);
                $configurationEditorScripts[$package] = array_unique($scripts);
            }
        }

        return $configurationEditorScripts;
    }

    public function getFrontendSettings(): array
    {
        $frontendSettingsList = array_map(static function (RegistryInterface $registry) {
            return $registry->getFrontendSettings();
        }, $this->collection);

        return ConfigurationUtility::mergeConfigurationStack($frontendSettingsList, excludeKeys: []);
    }

    public function addApiRouteResolvers(EntryRouteResolverInterface $entryResolver): void
    {
        foreach ($this->collection as $registry) {
            foreach ($registry->getApiRouteResolvers() as $domain => $resolver) {
                $entryResolver->registerResolver($domain, $resolver);
            }
        }
    }
}
