<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchemaInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class Initialization implements InitializationInterface
{
    protected const FRONTEND_SCRIPT_PATTERN = 'PKG:%s/res/assets/scripts/%s';

    protected const CONFIGURATION_DOCUMENT_FOLDER_PATTERN = 'PKG:%s/res/%s';

    protected const TEMPLATE_FOLDER_PATTERN = 'PKG:%s/res/%s';

    protected const PARTIAL_FOLDER_PATTERN = 'PKG:%s/res/%s';

    protected const LAYOUT_FOLDER_PATTERN = 'PKG:%s/res/%s';

    /** @var array<"core"|"distributor"|"collector",array<class-string<PluginInterface>,array<string|int,class-string<PluginInterface>>>> */
    protected const PLUGINS = [];

    /** @var array<class-string<ConfigurationDocumentMigrationInterface>> */
    protected const SCHEMA_MIGRATIONS = [];

    /** @var array<string> */
    protected const CONFIGURATION_EDITOR_SCRIPTS = [];

    /** @var array<string,array<string>> */
    protected const FRONTEND_SCRIPTS = [];

    /** @var array<string> */
    protected const CONFIGURATION_DOCUMENT_FOLDERS = ['configuration'];

    /** @var array<string,int> */
    protected const TEMPLATE_FOLDERS = ['templates/frontend' => 100];

    /** @var array<string,int> */
    protected const LAYOUT_FOLDERS = ['layouts/frontend' => 100];

    /** @var array<string,int> */
    protected const PARTIAL_FOLDERS = ['partials/frontend' => 100];

    /** @var array<string,int> */
    protected const BACKEND_TEMPLATE_FOLDERS = ['templates/backend' => 100];

    /** @var array<string,int> */
    protected const BACKEND_LAYOUT_FOLDERS = ['layouts/backend' => 100];

    /** @var array<string,int> */
    protected const BACKEND_PARTIAL_FOLDERS = ['partials/backend' => 100];

    public function __construct(
        protected string $packageName,
        protected string $schemaVersion,
        protected string $packageAlias = '',
        protected ?GlobalConfigurationSchemaInterface $globalConfigurationSchema = null,
    ) {
    }

    protected function getBackendSections(): array
    {
        return [];
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
        $this->initTemplateFolders($registry);
        $this->initPartialFolders($registry);
        $this->initLayoutFolders($registry);
        $this->initBackendSections($registry);

        $pluginLists = static::PLUGINS[$domain] ?? [];
        foreach ($pluginLists as $interface => $plugins) {
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

    public function getPackageAlias(): string
    {
        return $this->packageAlias;
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
            $paths = array_map(static function (string $path) use ($package): string {
                return sprintf(static::FRONTEND_SCRIPT_PATTERN, $package, $path);
            }, $paths);
            $registry->addFrontendScripts($type, $package, $paths);
        }
    }

    protected function initStaticConfigurationDocuments(RegistryInterface $registry): void
    {
        $package = $this->getFullPackageName();
        foreach (static::CONFIGURATION_DOCUMENT_FOLDERS as $path) {
            $registry->addStaticConfigurationDocumentFolderIdentifier(sprintf(static::CONFIGURATION_DOCUMENT_FOLDER_PATTERN, $package, $path));
        }
    }

    protected function initTemplateFolders(RegistryInterface $registry): void
    {
        $package = $this->getFullPackageName();
        foreach (static::TEMPLATE_FOLDERS as $folder => $priority) {
            $registry->getTemplateService()->addTemplateFolder(sprintf(static::TEMPLATE_FOLDER_PATTERN, $package, $folder), $priority);
        }

        foreach (static::BACKEND_TEMPLATE_FOLDERS as $folder => $priority) {
            $registry->getBackendTemplateService()->addTemplateFolder(sprintf(static::TEMPLATE_FOLDER_PATTERN, $package, $folder), $priority);
        }
    }

    protected function initPartialFolders(RegistryInterface $registry): void
    {
        $package = $this->getFullPackageName();
        foreach (static::PARTIAL_FOLDERS as $folder => $priority) {
            $registry->getTemplateService()->addPartialFolder(sprintf(static::PARTIAL_FOLDER_PATTERN, $package, $folder), $priority);
        }

        foreach (static::BACKEND_PARTIAL_FOLDERS as $folder => $priority) {
            $registry->getBackendTemplateService()->addPartialFolder(sprintf(static::PARTIAL_FOLDER_PATTERN, $package, $folder), $priority);
        }
    }

    protected function initLayoutFolders(RegistryInterface $registry): void
    {
        $package = $this->getFullPackageName();
        foreach (static::LAYOUT_FOLDERS as $folder => $priority) {
            $registry->getTemplateService()->addPartialFolder(sprintf(static::LAYOUT_FOLDER_PATTERN, $package, $folder), $priority);
        }

        foreach (static::BACKEND_LAYOUT_FOLDERS as $folder => $priority) {
            $registry->getBackendTemplateService()->addPartialFolder(sprintf(static::LAYOUT_FOLDER_PATTERN, $package, $folder), $priority);
        }
    }

    protected function initBackendSections(RegistryInterface $registry): void
    {
        foreach ($this->getBackendSections() as $section) {
            $registry->getBackendManager()->setSection($section);
        }
    }

    public function getGlobalConfigurationSchema(): ?GlobalConfigurationSchemaInterface
    {
        return $this->globalConfigurationSchema;
    }

    public function setGlobalConfigurationSchema(?GlobalConfigurationSchemaInterface $globalConfigurationSchema): void
    {
        $this->globalConfigurationSchema = $globalConfigurationSchema;
    }

    public function initMetaData(RegistryInterface $registry): void
    {
        $registry->addSchemaVersion($this->packageName, $this->schemaVersion);

        $registry->addPackageAlias($this->packageName, $this->packageAlias);

        $package = $this->getFullPackageName();
        if ($package !== $this->packageName) {
            $registry->addPackageAlias($package, $this->packageAlias);
        }

        $globalConfigurationSchema = $this->getGlobalConfigurationSchema();
        if ($globalConfigurationSchema instanceof GlobalConfigurationSchemaInterface) {
            $registry->addGlobalConfigurationSchemaForPackage(
                $this->packageAlias !== '' ? $this->packageAlias : $this->packageName,
                $globalConfigurationSchema
            );
        }

        $this->initConfigurationEditorScripts($registry);
        $this->initFrontendScripts($registry);
        $this->initStaticConfigurationDocuments($registry);

        foreach (static::SCHEMA_MIGRATIONS as $migrationClass) {
            $migration = new $migrationClass();
            $registry->addSchemaMigration($migration);
        }
    }
}
