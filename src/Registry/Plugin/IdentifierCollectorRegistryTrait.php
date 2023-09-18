<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
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
        /** @var array<IdentifierCollectorInterface> */
        return $this->getAllPlugins(IdentifierCollectorInterface::class, [$configuration]);
    }

    public function getIdentifierCollector(string $keyword, ConfigurationInterface $configuration): ?IdentifierCollectorInterface
    {
        /** @var ?IdentifierCollectorInterface */
        return $this->getPlugin($keyword, IdentifierCollectorInterface::class, [$configuration]);
    }

    public function deleteIdentifierCollector(string $keyword): void
    {
        $this->deletePlugin($keyword, IdentifierCollectorInterface::class);
    }

    public function getIdentifierCollectorSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();

        $collectorSchema = new IdentifierCollectorSchema();
        foreach ($this->getAllPluginClasses(IdentifierCollectorInterface::class) as $key => $class) {
            $collectorSchema->addItem($key, $class::getSchema());
        }

        $schema->addProperty(ConfigurationInterface::KEY_IDENTIFIER_COLLECTORS, $collectorSchema);

        return $schema;
    }
}
