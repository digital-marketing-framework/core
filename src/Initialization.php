<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class Initialization implements InitializationInterface
{
    /** @var array<string,array<class-string<PluginInterface>,array<string|int,class-string<PluginInterface>>>> */
    protected const PLUGINS = [];

    /** @var array<class-string<ConfigurationDocumentMigrationInterface>> */
    protected const SCHEMA_MIGRATIONS = [];

    /** @var array<string> */
    protected const CONFIGURATION_EDITOR_SCRIPTS = [];

    /** @var array<string,array<string>> */
    protected const FRONTEND_SCRIPTS = [];

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

    public function getFullPackageName(): string
    {
        $package = $this->packageName;
        if (!str_contains($package, '/')) {
            $package = 'digital-marketing-framework/' . $package;
        }

        return $package;
    }

    protected function initConfigurationEditorScripts(RegistryInterface $registry): void
    {
        if (static::CONFIGURATION_EDITOR_SCRIPTS !== []) {
            $registry->addConfigurationEditorScripts($this->packageName, static::CONFIGURATION_EDITOR_SCRIPTS);
        }
    }

    protected function initFrontendScripts(RegistryInterface $registry): void
    {
        $package = $this->getFullPackageName();
        foreach (static::FRONTEND_SCRIPTS as $type => $paths) {
            $registry->addFrontendScripts($type, $package, $paths);
        }
    }

    public function initMetaData(RegistryInterface $registry): void
    {
        $registry->addSchemaVersion($this->packageName, $this->schemaVersion);

        $registry->addPackageAlias($this->packageName, $this->packageAlias);

        $this->initConfigurationEditorScripts($registry);
        $this->initFrontendScripts($registry);

        foreach (static::SCHEMA_MIGRATIONS as $migrationClass) {
            $migration = new $migrationClass();
            $registry->addSchemaMigration($migration);
        }
    }
}
