<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\EvaluationSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryTrait;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineInterface;

trait ConfigurationSchemaRegistryTrait
{
    use DataProcessorRegistryTrait;
    use TemplateEngineRegistryTrait;

    /** @var array<string,string> */
    protected array $schemaVersion = [];

    protected SchemaDocument $schemaDocument;

    abstract public function getConfigurationDocumentManager(): ConfigurationDocumentManagerInterface;

    public function addSchemaVersion(string $key, string $version): void
    {
        $this->schemaVersion[$key] = $version;
    }

    /**
     * @return array<string,string>
     */
    protected function getIncludeValueSet(): array
    {
        $includes = [];
        $configurationDocumentManager = $this->getConfigurationDocumentManager();
        $documentIdentifiers = $configurationDocumentManager->getDocumentIdentifiers();
        foreach ($documentIdentifiers as $documentIdentifier) {
            $metaData = $configurationDocumentManager->getDocumentInformation($documentIdentifier);
            $label = '[' . $documentIdentifier . ']';
            if ($metaData['name'] !== $documentIdentifier) {
                $label = $metaData['name'] . ' ' . $label;
            }

            $includes[$documentIdentifier] = $label;
        }

        uksort($includes, static function (string $key1, string $key2) {
            $prefix1 = substr($key1, 0, 4);
            $prefix2 = substr($key2, 0, 4);
            if ($prefix1 === 'SYS:') {
                if ($prefix2 !== 'SYS:') {
                    return -1;
                }
            } elseif ($prefix2 === 'SYS:') {
                return 1;
            } elseif (preg_match('/^[A-Z]{3}:$/', $prefix1)) {
                if (!preg_match('/^[A-Z]{3}:$/', $prefix2)) {
                    return -1;
                }
            } elseif (preg_match('/^[A-Z]{3}:$/', $prefix2)) {
                return -1;
            }

            return $key1 <=> $key2;
        });

        return $includes;
    }

    public function addConfigurationSchema(SchemaDocument $schemaDocument): void
    {
        $schemaDocument->addCustomType($this->getDataMapperSchema(), DataMapperSchema::TYPE);
        $schemaDocument->addCustomType(new ValueSchema(), ValueSchema::TYPE);
        $schemaDocument->addCustomType($this->getValueSourceSchema(), ValueSourceSchema::TYPE);
        $schemaDocument->addCustomType($this->getValueModifierSchema(), ValueModifierSchema::TYPE);
        $schemaDocument->addCustomType($this->getEvaluationSchema(), EvaluationSchema::TYPE);
        $schemaDocument->addCustomType($this->getComparisonSchema(), ComparisonSchema::TYPE);

        $schemaDocument->addCustomType($this->getTemplateSchema(TemplateEngineInterface::FORMAT_PLAIN_TEXT), TemplateEngineInterface::TYPE_PLAIN_TEXT);
        $schemaDocument->addCustomType($this->getTemplateSchema(TemplateEngineInterface::FORMAT_HTML), TemplateEngineInterface::TYPE_HTML);

        foreach ($this->getIncludeValueSet() as $documentIdentifier => $label) {
            $schemaDocument->addValueToValueSet('document/all', $documentIdentifier, $label);
        }

        $mainSchema = $schemaDocument->getMainSchema();
        $mainSchema->getRenderingDefinition()->setLabel('Digital Marketing');

        $metaDataSchema = new ContainerSchema();
        $metaDataSchema->getRenderingDefinition()->setLabel('Document');
        $nameSchema = new StringSchema();
        $metaDataSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME, $nameSchema);
        $includeSchema = new StringSchema();
        $includeSchema->getAllowedValues()->addValueSet('document/all');
        $includeSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $includeSchema->getRenderingDefinition()->setLabel('Document');
        $includeListSchema = new ListSchema($includeSchema);
        $includeListSchema->getRenderingDefinition()->setNavigationItem(false);
        $includeListSchema->setDynamicOrder(true);
        $metaDataSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_INCLUDES, $includeListSchema);
        $mainSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_META_DATA, $metaDataSchema);

        $valueMapSchema = new MapSchema(new StringSchema());
        $valuesMapKeySchema = new StringSchema('mapName');
        $valuesMapKeySchema->getRenderingDefinition()->setLabel('Value Map Name');
        $valueMapsSchema = new MapSchema($valueMapSchema, $valuesMapKeySchema);

        $mainSchema->addProperty(ConfigurationInterface::KEY_VALUE_MAPS, $valueMapsSchema);
        $mainSchema->addProperty(ConfigurationInterface::KEY_IDENTIFIER, $this->getIdentifierCollectorSchema());

        foreach ($this->schemaVersion as $key => $version) {
            $schemaDocument->addVersion($key, $version);
        }
    }

    /**
     * This method will produce the schema document for this registry. There may be others.
     * If you want to produce the schema for all registries, create your own SchemaDocument
     * and call addConfigurationSchema() on all DMF registries in the system.
     */
    public function getConfigurationSchema(): SchemaDocument
    {
        if (!isset($this->schemaDocument)) {
            $this->schemaDocument = new SchemaDocument();
            $this->addConfigurationSchema($this->schemaDocument);
        }

        return $this->schemaDocument;
    }
}
