<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

trait GlobalConfigurationSchemaRegistryTrait
{
    /** @var array<string,GlobalConfigurationSchemaInterface> */
    protected array $globalConfigurationSchemaList = [];

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
        $mainSchema = $globalConfigurationSchemaDocument->getMainSchema();
        $mainSchema->getRenderingDefinition()->setLabel('Global Settings');
        $mainSchema->getRenderingDefinition()->setGeneralDescription('Use placeholders for environment variables: @{MY_ENV_VAR}');

        foreach ($this->globalConfigurationSchemaList as $key => $schema) {
            $property = $mainSchema->addProperty($key, $schema);
            $property->setWeight($schema->getWeight());
        }
    }

    public function getGlobalConfigurationSchemaDocument(): SchemaDocument
    {
        return $this->getRegistryCollection()->getGlobalConfigurationSchemaDocument();
    }
}
