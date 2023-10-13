<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class Initialization implements InitializationInterface
{
    protected const PLUGINS = [];

    protected const SCHEMA_MIGRATIONS = [];

    public function __construct(
        protected string $packageName,
        protected string $schemaVersion,
        protected string $packageAlias = ''
    ) {
    }

    /**
     * @return array<mixed>
     */
    protected function getAdditionalPluginArguments(string $interface, string $pluginClass, RegistryInterface $registry): array
    {
        return [];
    }

    public function initPlugins(string $domain, RegistryInterface $registry): void
    {
        $plugins = static::PLUGINS[$domain] ?? [];
        foreach ($plugins as $interface => $plugins) {
            foreach ($plugins as $keyword => $class) {
                $registry->registerPlugin(
                    $interface,
                    $class,
                    $this->getAdditionalPluginArguments($interface, $class, $registry),
                    $keyword
                );
            }
        }
    }

    public function initServices(string $domain, RegistryInterface $registry): void
    {
    }

    public function initGlobalConfiguration(string $domain, RegistryInterface $registry): void
    {
    }

    public function initMetaData(RegistryInterface $registry): void
    {
        $registry->addSchemaVersion($this->packageName, $this->schemaVersion);

        $registry->addPackageAlias($this->packageName, $this->packageAlias);

        foreach (static::SCHEMA_MIGRATIONS as $migrationClass) {
            /** @var ConfigurationDocumentMigrationInterface $migration */
            $migration = new $migrationClass();
            $registry->addSchemaMigration($migration);
        }
    }
}
