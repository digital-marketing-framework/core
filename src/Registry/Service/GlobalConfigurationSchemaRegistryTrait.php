<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\EvaluationSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryTrait;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineInterface;

trait GlobalConfigurationSchemaRegistryTrait
{
    /** @var array<string,SchemaInterface> */
    protected array $globalConfigurationSchemaList = [];

    protected SchemaDocument $globalConfigurationSchemaDocument;

    /**
     * @return array<string,string>
     */
    abstract protected function getIncludeValueSet(): array;

    public function addGlobalConfigurationSchemaForPackage(string $packageName, SchemaInterface $schema): void
    {
        $this->globalConfigurationSchemaList[$packageName] = $schema;
    }

    public function addGlobalConfigurationSchema(SchemaDocument $globalConfigurationSchemaDocument): void
    {
        foreach ($this->getIncludeValueSet() as $documentIdentifier => $label) {
            $globalConfigurationSchemaDocument->addValueToValueSet('document/all', $documentIdentifier, $label);
        }

        $mainSchema = $globalConfigurationSchemaDocument->getMainSchema();
        $mainSchema->getRenderingDefinition()->setLabel('Global Settings');

        foreach ($this->globalConfigurationSchemaList as $key => $schema) {
            $mainSchema->addProperty($key, $schema);
        }
    }

    /**
     * This method will produce the global configuration schema document for this registry. There may be others.
     * If you want to produce the global configuration schema for all registries, create your own SchemaDocument
     * and call addGlobalConfigurationSchema() on all DMF registries in the system.
     */
    public function getGlobalConfigurationSchema(): SchemaDocument
    {
        if (!isset($this->globalConfigurationSchemaDocument)) {
            $this->globalConfigurationSchemaDocument = new SchemaDocument();
            $this->addGlobalConfigurationSchema($this->globalConfigurationSchemaDocument);
        }

        return $this->globalConfigurationSchemaDocument;
    }
}
