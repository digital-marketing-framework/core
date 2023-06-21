<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\EvaluationSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\Comparison;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

trait ConfigurationSchemaRegistryTrait
{
    protected SchemaDocument $schemaDocument;

    abstract public function getConfigurationDocumentManager(): ConfigurationDocumentManagerInterface;

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
        uksort($includes, function(string $key1, string $key2) {
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
        $schemaDocument->addCustomType($this->getComparisonSchema(), 'COMPARISON');

        foreach ($this->getIncludeValueSet() as $documentIdentifier => $label) {
            $schemaDocument->addValueToValueSet('document/all', $documentIdentifier, $label);
        }

        $mainSchema = $schemaDocument->getMainSchema();
        $mainSchema->getRenderingDefinition()->setLabel('Digital Marketing');

        $metaDataSchema = new ContainerSchema();
        $metaDataSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME, new StringSchema());
        $includeSchema = new StringSchema();
        $includeSchema->getAllowedValues()->addValueSet('document/all');
        $includeSchema->getRenderingDefinition()->setFormat('select');
        $includeSchema->getRenderingDefinition()->setLabel('INCLUDE');
        $includeListSchema = new ListSchema($includeSchema);
        $includeListSchema->getRenderingDefinition()->setNavigationItem(false);
        $metaDataSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_INCLUDES, $includeListSchema);
        $mainSchema->addProperty(ConfigurationDocumentManagerInterface::KEY_META_DATA, $metaDataSchema);

        $valueMapsSchema = new MapSchema(new MapSchema(new StringSchema()));

        $mainSchema->addProperty(ConfigurationInterface::KEY_VALUE_MAPS, $valueMapsSchema);
        $mainSchema->addProperty(ConfigurationInterface::KEY_IDENTIFIER, $this->getIdentifierCollectorSchema());
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
