<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\IdentifierCollector\IdentifierCollectorInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;

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

    protected function addIdentifierCollectorSchemas(SchemaDocument $schemaDocument): void
    {
        foreach ($this->getAllPluginClasses(IdentifierCollectorInterface::class) as $keyword => $class) {
            $schema = $class::getSchema();
            $integration = $class::getIntegrationName();
            $label = $class::getLabel();

            $schemaDocument->addValueToValueSet('identifierCollector/all', $keyword);
            $schemaDocument->addValueToValueSet('identifierCollector/' . $integration . '/all', $keyword);

            $integrationSchema = $this->getIntegrationSchemaForPluginClass($schemaDocument, $class);
            $integrationIdentifierSchema = $integrationSchema->getProperty(ConfigurationInterface::KEY_IDENTIFIERS);
            if (!$integrationIdentifierSchema instanceof ContainerSchema) {
                $integrationIdentifierSchema = new ContainerSchema();
                $integrationIdentifierSchema->getRenderingDefinition()->setIcon('identification');
                $integrationIdentifierSchema->getRenderingDefinition()->setLabel('Identification');
                $integrationSchema->addProperty(ConfigurationInterface::KEY_IDENTIFIERS, $integrationIdentifierSchema);
            }
            $property = $integrationIdentifierSchema->addProperty($keyword, $schema);
            $property->getRenderingDefinition()->setLabel($label);
        }
    }
}
