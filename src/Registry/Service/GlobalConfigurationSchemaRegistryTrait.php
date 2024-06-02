<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

trait GlobalConfigurationSchemaRegistryTrait
{
    /** @var array<string,GlobalConfigurationSchemaInterface> */
    protected array $globalConfigurationSchemaList = [];

    protected SchemaDocument $globalConfigurationSchemaDocument;

    /**
     * @return array<string,string>
     */
    abstract protected function getIncludeValueSet(): array;

    public function addGlobalConfigurationSchemaForPackage(string $packageName, GlobalConfigurationSchemaInterface $schema): void
    {
        $this->globalConfigurationSchemaList[$packageName] = $schema;
    }

    public function addGlobalConfigurationSchemaDocument(SchemaDocument $globalConfigurationSchemaDocument): void
    {
        foreach ($this->getIncludeValueSet() as $documentIdentifier => $label) {
            $globalConfigurationSchemaDocument->addValueToValueSet('document/all', $documentIdentifier, $label);
        }

        $mainSchema = $globalConfigurationSchemaDocument->getMainSchema();
        $mainSchema->getRenderingDefinition()->setLabel('Global Settings');

        foreach ($this->globalConfigurationSchemaList as $key => $schema) {
            $property = $mainSchema->addProperty($key, $schema);
            $property->setWeight($schema->getWeight());
        }
    }

    /**
     * This method will produce the global configuration schema document for this registry. There may be others.
     * If you want to produce the global configuration schema for all registries, create your own SchemaDocument
     * and call addGlobalConfigurationSchema() on all DMF registries in the system.
     */
    public function getGlobalConfigurationSchemaDocument(): SchemaDocument
    {
        if (!isset($this->globalConfigurationSchemaDocument)) {
            $this->globalConfigurationSchemaDocument = new SchemaDocument();
            $this->addGlobalConfigurationSchemaDocument($this->globalConfigurationSchemaDocument);
        }

        return $this->globalConfigurationSchemaDocument;
    }
}
