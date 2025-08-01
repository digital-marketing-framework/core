<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\GlobalSettingsInterface;
use DigitalMarketingFramework\Core\Package\PackageAliases;
use DigitalMarketingFramework\Core\Package\PackageAliasesInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerProperty;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

class DefaultGlobalConfiguration implements GlobalConfigurationInterface
{
    /** @var array<string,mixed> */
    protected array $config = [];

    protected PackageAliasesInterface $packageAliases;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
        $this->packageAliases = new PackageAliases();
    }

    public function getGlobalSettings(string $class, ...$arguments): GlobalSettingsInterface
    {
        // @phpstan-ignore-next-line We test the class for good measure as it can come from a third-party package
        if (!is_a($class, GlobalSettingsInterface::class, true)) {
            throw new DigitalMarketingFrameworkException(sprintf('Unknown global settings class "%s"', $class));
        }

        $settings = $this->registry->createObject($class, $arguments);
        $packageName = $settings->getPackageName();
        $component = $settings->getComponentName();

        $packageName = $this->packageAliases->resolveAlias($packageName);
        $globalComponentSettings = $this->get($packageName, []);

        $globalConfigurationSchemaDocument = $this->registry->getGlobalConfigurationSchemaDocument();
        $globalConfigurationSchema = $globalConfigurationSchemaDocument->getMainSchema();
        $globalComponentConfigurationSchemaProperty = $globalConfigurationSchema->getProperty($packageName);
        if ($globalComponentConfigurationSchemaProperty instanceof ContainerProperty) {
            $globalComponentSchema = $globalComponentConfigurationSchemaProperty->getSchema();
            $globalComponentDefaultSettings = $this->registry->getSchemaProcessor()->getDefaultValue(
                $globalConfigurationSchemaDocument,
                $globalComponentSchema
            );
        } else {
            $globalComponentDefaultSettings = [];
        }

        $globalSettings = ConfigurationUtility::mergeConfigurationStack(
            [
                $globalComponentDefaultSettings,
                $globalComponentSettings,
            ],
            excludeKeys: []
        );

        if ($component !== '') {
            $globalSettings = $globalSettings[$component] ?? [];
        }

        $settings->injectSettings($globalSettings);

        return $settings;
    }

    public function get(string $key, mixed $default = null, bool $resolvePlaceholders = true): mixed
    {
        $key = $this->packageAliases->resolveAlias($key);

        $value = array_key_exists($key, $this->config) ? $this->config[$key] : $default;

        if ($resolvePlaceholders) {
            $value = $this->registry->getEnvironmentService()->insertEnvironmentVariables($value);
        }

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $key = $this->packageAliases->resolveAlias($key);
        $this->config[$key] = $value;
    }

    public function setPackageAliases(PackageAliasesInterface $packageAliases): void
    {
        $this->packageAliases = $packageAliases;
    }
}
