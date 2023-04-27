<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\IdentifierCollector\IdentifierCollectorSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\IdentifierCollector\IdentifierCollectorInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

trait IdentifierCollectorRegistryTrait
{
    use PluginRegistryTrait;

    public function registerIdentifierCollector(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(IdentifierCollectorInterface::class, $class, $additionalArguments, $keyword);
    }
    
    /**
     * @return array<IdentifierCollectorInterface>
     */
    public function getAllIdentifierCollectors(ConfigurationInterface $configuration): array
    {
        return $this->getAllPlugins(IdentifierCollectorInterface::class, [$configuration]);
    }

    public function getIdentifierCollector(string $keyword, ConfigurationInterface $configuration): ?IdentifierCollectorInterface
    {
        return $this->getPlugin($keyword, IdentifierCollectorInterface::class, [$configuration]);
    }

    public function deleteIdentifierCollector(string $keyword): void
    {
        $this->deletePlugin($keyword, IdentifierCollectorInterface::class);
    }

    public function getIdentifierCollectorDefaultConfigurations(): array
    {
        $result = [];
        foreach ($this->pluginClasses[IdentifierCollectorInterface::class] ?? [] as $key => $class) {
            $result[$key] = $class::getDefaultConfiguration();
        }
        return $result;
    }

    public function getIdentifierCollectorSchema(): SchemaInterface
    {
        return new IdentifierCollectorSchema($this);
    }
}
