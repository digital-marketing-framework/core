<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;
use DigitalMarketingFramework\Core\Plugin\IntegrationPluginInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;
use DigitalMarketingFramework\Core\Registry\RegistryException;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

trait PluginRegistryTrait
{
    /** @var array<class-string<PluginInterface>,array<string,class-string<PluginInterface>>> */
    protected array $pluginClasses = [];

    /**
     * @var array<class-string<PluginInterface>,array<class-string<PluginInterface>,array<mixed>>>
     */
    protected array $pluginAdditionalArguments = [];

    abstract public function createObject(string $class, array $arguments = []): object;

    abstract protected function classValidation(string $class, string $interface): void;

    abstract protected function interfaceValidation(string $interface, string $parentInterface): void;

    abstract public function getConfigurationSchemaDocument(): SchemaDocument;

    abstract public function getSchemaProcessor(): SchemaProcessorInterface;

    public function processPluginAwareness(PluginInterface $plugin): void
    {
        $schemaDocument = null;
        if ($plugin instanceof ConfigurablePluginInterface) {
            $schema = $plugin::getSchema();
            $schemaDocument = $this->getConfigurationSchemaDocument();
            $defaults = $this->getSchemaProcessor()->getDefaultValue($schemaDocument, $schema);

            if (!is_array($defaults)) {
                throw new DigitalMarketingFrameworkException('default configuration has to be an array');
            }

            $plugin->setDefaultConfiguration($defaults);
        }

        if ($plugin instanceof IntegrationPluginInterface) {
            $schemaDocument ??= $this->getConfigurationSchemaDocument();
            $integrationName = $plugin->getIntegrationInfo()->getName();
            $schema = $schemaDocument->getMainSchema()
                ->getProperty(ConfigurationInterface::KEY_INTEGRATIONS)
                ?->getSchema()
                ?->getProperty($integrationName)
                ?->getSchema();

            $defaults = null;
            if ($schema !== null) {
                $defaults = $this->getSchemaProcessor()->getDefaultValue($schemaDocument, $schema);
            }

            if (!is_array($defaults)) {
                throw new DigitalMarketingFrameworkException('default integration configuration has to be an array');
            }

            $plugin->setDefaultIntegrationConfiguration($defaults);
        }
    }

    public function getPlugin(string $keyword, string $interface, array $arguments = []): ?PluginInterface
    {
        $class = $this->getPluginClass($interface, $keyword);
        $additionalArguments = $this->pluginAdditionalArguments[$interface][$keyword] ?? [];

        if ($class === null && $this->checkKeywordAsClass($keyword, $interface)) {
            $class = $keyword;
            $keyword = GeneralUtility::getPluginKeyword($keyword, $interface) ?: $keyword;
            $additionalArguments = [];
        }

        if ($class && class_exists($class)) {
            $constructorArguments = [$keyword, $this];
            array_push($constructorArguments, ...$arguments);
            array_push($constructorArguments, ...$additionalArguments);
            /** @var PluginInterface */
            $plugin = $this->createObject($class, $constructorArguments);

            $this->processPLuginAwareness($plugin);

            return $plugin;
        }

        return null;
    }

    public function getAllPlugins(string $interface, array $arguments = []): array
    {
        $result = [];
        foreach (array_keys($this->getAllPluginClasses($interface)) as $keyword) {
            $result[$keyword] = $this->getPlugin($keyword, $interface, $arguments);
        }

        $this->sortPlugins($result);

        return $result;
    }

    public function getAllPluginClasses(string $interface): array
    {
        $classes = $this->pluginClasses[$interface] ?? [];
        uasort($classes, static function (string $a, string $b) {
            return $a::getWeight() <=> $b::getWeight();
        });

        return $classes;
    }

    public function sortPlugins(array &$plugins): void
    {
        uasort($plugins, static function (PluginInterface $a, PluginInterface $b) {
            return $a->getConfiguredWeight() <=> $b->getConfiguredWeight();
        });
    }

    public function getPluginClass(string $interface, string $keyword): ?string
    {
        return $this->pluginClasses[$interface][$keyword] ?? null;
    }

    public function registerPlugin(string $interface, string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        if ($keyword === '' || is_numeric($keyword)) {
            $keyword = GeneralUtility::getPluginKeyword($class, $interface) ?: $keyword;
        }

        $this->interfaceValidation($interface, PluginInterface::class);
        $this->classValidation($class, $interface);
        $this->pluginClasses[$interface][$keyword] = $class;
        $this->pluginAdditionalArguments[$interface][$keyword] = $additionalArguments;
    }

    public function deletePlugin(string $keyword, string $interface): void
    {
        if (isset($this->pluginClasses[$interface][$keyword])) {
            unset($this->pluginClasses[$interface][$keyword]);
        }

        if (isset($this->pluginAdditionalArguments[$interface][$keyword])) {
            unset($this->pluginAdditionalArguments[$interface][$keyword]);
        }
    }

    protected function checkKeywordAsClass(string $keyword, string $interface): bool
    {
        $result = false;
        if (class_exists($keyword)) {
            try {
                $this->classValidation($keyword, $interface);
                $result = true;
            } catch (RegistryException) {
                // keyword is not a class (or it is not implementing the desired interface)
            }
        }

        return $result;
    }
}
