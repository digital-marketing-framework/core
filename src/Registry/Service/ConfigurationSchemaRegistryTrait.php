<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\Integration\IntegrationInfo;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryTrait;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ConditionReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataMapperGroupReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\FieldContextSelectionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ConditionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperGroupSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineInterface;
use DigitalMarketingFramework\Core\Utility\MapUtility;

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

    protected function getIntegrationSchema(
        SchemaDocument $schemaDocument,
        ?string $integrationName = null,
        ?string $integrationLabel = null,
        ?int $weight = null,
        ?string $icon = null
    ): ContainerSchema {
        $mainSchema = $schemaDocument->getMainSchema();
        $integrationsSchema = $mainSchema->getProperty(ConfigurationInterface::KEY_INTEGRATIONS)?->getSchema();
        if (!$integrationsSchema instanceof ContainerSchema) {
            $integrationsSchema = new ContainerSchema();
            $integrationsSchema->getRenderingDefinition()->sortAlphabetically(true);
            $integrationsSchema->getRenderingDefinition()->setIcon('integrations');
            $mainSchema->addProperty(ConfigurationInterface::KEY_INTEGRATIONS, $integrationsSchema);
        }

        if ($integrationName === null) {
            return $integrationsSchema;
        }

        $integrationImplementationSchema = $integrationsSchema->getProperty($integrationName)?->getSchema();
        if (!$integrationImplementationSchema instanceof ContainerSchema) {
            $integrationImplementationSchema = new ContainerSchema();
            if ($integrationLabel !== null) {
                $integrationImplementationSchema->getRenderingDefinition()->setLabel($integrationLabel);
            }

            if ($icon !== null) {
                $integrationImplementationSchema->getRenderingDefinition()->setIcon($icon);
            }

            $property = $integrationsSchema->addProperty($integrationName, $integrationImplementationSchema);
            if ($weight !== null) {
                $property->setWeight($weight);
            }
        }

        return $integrationImplementationSchema;
    }

    protected function getIntegrationSchemaForPlugin(SchemaDocument $schemaDocument, IntegrationInfo $integrationInfo): ContainerSchema
    {
        return $this->getIntegrationSchema(
            $schemaDocument,
            $integrationInfo->getName(),
            $integrationInfo->getLabel(),
            $integrationInfo->getWeight(),
            $integrationInfo->getIcon()
        );
    }

    protected function getGeneralIntegrationSchema(SchemaDocument $schemaDocument): ContainerSchema
    {
        return $this->getIntegrationSchema(
            $schemaDocument,
            integrationName: ConfigurationInterface::KEY_GENERAL_INTEGRATION,
            weight: IntegrationInfo::WEIGHT_TOP,
            icon: 'general'
        );
    }

    protected function getDataProcessingSchema(SchemaDocument $schemaDocument): ContainerSchema
    {
        $mainSchema = $schemaDocument->getMainSchema();
        $dataProcessingSchema = $mainSchema->getProperty(ConfigurationInterface::KEY_DATA_PROCESSING)?->getSchema();
        if (!$dataProcessingSchema instanceof ContainerSchema) {
            $dataProcessingSchema = new ContainerSchema();
            $dataProcessingSchema->getRenderingDefinition()->setIcon('data-processing');
            $mainSchema->addProperty(ConfigurationInterface::KEY_DATA_PROCESSING, $dataProcessingSchema);
        }

        return $dataProcessingSchema;
    }

    public function addConfigurationSchema(SchemaDocument $schemaDocument): void
    {
        // complex values
        $schemaDocument->addCustomType(new ValueSchema(), ValueSchema::TYPE);
        $schemaDocument->addCustomType($this->getValueSourceSchema(), ValueSourceSchema::TYPE);
        $schemaDocument->addCustomType($this->getValueModifierSchema(), ValueModifierSchema::TYPE);

        // complex conditions
        $schemaDocument->addCustomType($this->getConditionSchema(), ConditionSchema::TYPE);
        $schemaDocument->addCustomType($this->getConditionSchema(withContext: true), ConditionSchema::TYPE_WITH_CONTEXT);
        $schemaDocument->addCustomType(new ConditionReferenceSchema(), ConditionReferenceSchema::TYPE);
        $schemaDocument->addCustomType($this->getComparisonSchema(), ComparisonSchema::TYPE);

        // data set processing
        $schemaDocument->addCustomType($this->getDataMapperSchema(), DataMapperSchema::TYPE);
        $schemaDocument->addCustomType($this->getDataMapperGroupSchema(), DataMapperGroupSchema::TYPE);
        $schemaDocument->addCustomType(new DataMapperGroupReferenceSchema(), DataMapperGroupReferenceSchema::TYPE);

        // templating
        $schemaDocument->addCustomType($this->getTemplateSchema(TemplateEngineInterface::FORMAT_PLAIN_TEXT), TemplateEngineInterface::TYPE_PLAIN_TEXT);
        $schemaDocument->addCustomType($this->getTemplateSchema(TemplateEngineInterface::FORMAT_HTML), TemplateEngineInterface::TYPE_HTML);

        // field context selection
        $schemaDocument->addCustomType(new FieldContextSelectionSchema(true), FieldContextSelectionSchema::TYPE_INPUT);
        $schemaDocument->addCustomType(new FieldContextSelectionSchema(false), FieldContextSelectionSchema::TYPE_OUTPUT);

        // document IDs
        foreach ($this->getIncludeValueSet() as $documentIdentifier => $label) {
            $schemaDocument->addValueToValueSet('document/all', $documentIdentifier, $label);
        }

        // schema versions
        foreach ($this->schemaVersion as $key => $version) {
            $schemaDocument->addVersion($key, $version);
        }

        // main schema
        $mainSchema = $schemaDocument->getMainSchema();
        $mainSchema->getRenderingDefinition()->setLabel('Digital Marketing');

        // meta data
        $metaDataSchema = new ContainerSchema();
        $metaDataSchema->getRenderingDefinition()->setIcon('document');
        $metaDataSchema->getRenderingDefinition()->setLabel('Document');

        $nameSchema = new StringSchema();
        $nameSchema->getRenderingDefinition()->setIcon('document-name');
        $metaDataSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME, $nameSchema);

        $strictValidationSchema = new BooleanSchema(false);
        $metaDataSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_DOCUMENT_STRICT_VALIDATION, $strictValidationSchema);

        $includeSchema = new StringSchema();
        $includeSchema->getRenderingDefinition()->setIcon('document-include');
        $includeSchema->getAllowedValues()->addValueSet('document/all');
        $includeSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $includeSchema->getRenderingDefinition()->setLabel('Document');
        $includeListSchema = new ListSchema($includeSchema);
        $includeListSchema->getRenderingDefinition()->setIcon('document-includes');
        $includeListSchema->getRenderingDefinition()->setNavigationItem(false);
        $includeListSchema->setDynamicOrder(true);
        $metaDataSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_INCLUDES, $includeListSchema);

        $mainSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_META_DATA, $metaDataSchema);

        // data processing
        $dataProcessingSchema = $this->getDataProcessingSchema($schemaDocument);

        // data processing - value maps
        $valueMapKeySchema = new StringSchema();
        $valueMapKeySchema->getRenderingDefinition()->setLabel('Original Value');
        $valueMapValueSchema = new StringSchema();
        $valueMapValueSchema->getRenderingDefinition()->setLabel(sprintf('Mapped Value ({../%s})', MapUtility::KEY_KEY));
        $valueMapSchema = new MapSchema($valueMapValueSchema, $valueMapKeySchema);
        $valueMapSchema->getRenderingDefinition()->setIcon('value-map');

        $valueMapsKeySchema = new StringSchema('valueMapName');
        $valueMapsKeySchema->getRenderingDefinition()->setLabel('Value Map Name');

        $valueMapsSchema = new MapSchema($valueMapSchema, $valueMapsKeySchema);
        $valueMapsSchema->getRenderingDefinition()->setLabel('Value Mappings');
        $valueMapsSchema->getRenderingDefinition()->setIcon('value-maps');

        $dataProcessingSchema->addProperty(ConfigurationInterface::KEY_VALUE_MAPS, $valueMapsSchema);

        // data processing - conditions
        $conditionListSchema = new MapSchema(new CustomSchema(ConditionSchema::TYPE_WITH_CONTEXT));
        $conditionListSchema->getRenderingDefinition()->setIcon('conditions');
        $dataProcessingSchema->addProperty(ConfigurationInterface::KEY_CONDITIONS, $conditionListSchema);

        // data processing - data mapper groups
        $dataMapperGroupListSchema = new MapSchema(new CustomSchema(DataMapperGroupSchema::TYPE));
        $dataMapperGroupListSchema->getRenderingDefinition()->setIcon('data-mapper-groups');
        $dataMapperGroupListSchema->getRenderingDefinition()->setLabel('Field Mappings');
        $dataProcessingSchema->addProperty(ConfigurationInterface::KEY_DATA_MAPPER_GROUPS, $dataMapperGroupListSchema);

        // identifier
        $this->addIdentifierCollectorSchemas($schemaDocument);
    }

    /**
     * NOTE This method will produce the schema document for this registry. There may be others.
     *      If you want to produce the schema for all registries, create your own SchemaDocument
     *      and call addConfigurationSchema() on all DMF registries in the system.
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
