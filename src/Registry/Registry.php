<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Cache\DataCacheAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
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
use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationDocumentManagerRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\FileStorageRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryTrait;

class Registry implements RegistryInterface
{
    use GlobalConfigurationRegistryTrait;

    use LoggerFactoryRegistryTrait;
    use ContextRegistryTrait;
    use CacheRegistryTrait;
    use ConfigurationDocumentManagerRegistryTrait;
    use FileStorageRegistryTrait;

    use DataProcessorRegistryTrait;
    use IdentifierCollectorRegistryTrait;

    protected SchemaDocument $schemaDocument;

    protected function processObjectAwareness(object $object): void
    {
        if ($object instanceof GlobalConfigurationAwareInterface) {
            $object->setGlobalConfiguration($this->getGlobalConfiguration());
        }
        if ($object instanceof LoggerAwareInterface) {
            $logger = $this->getLoggerFactory()->getLogger(get_class($object));
            $object->setLogger($logger);
        }
        if ($object instanceof ContextAwareInterface) {
            $object->setContext($this->getContext());
        }
        if ($object instanceof DataCacheAwareInterface) {
            $object->setCache($this->getCache());
        }
        if ($object instanceof DataProcessorAwareInterface) {
            $object->setDataProcessor($this->getDataProcessor());
        }
        if ($object instanceof FileStorageAwareInterface) {
            $object->setFileStorage($this->getFileStorage());
        }
    }

    public function createObject(string $class, array $arguments = []): object
    {
        if (!class_exists($class)) {
            throw new RegistryException('Class "' . $class . '" is unknown!');
        }
        $object = new $class(...$arguments);
        $this->processObjectAwareness($object);
        return $object;
    }

    protected function classValidation(string $class, string $interface): void
    {
        if (!class_exists($class)) {
            throw new RegistryException('class "' . $class . '" does not exist.');
        }
        if (!in_array($interface, class_implements($class))) {
            throw new RegistryException('class "' . $class . '" has to implement interface "' . $interface . '".');
        }
    }

    protected function interfaceValidation(string $interface, string $parentInterface): void
    {
        if (!interface_exists($interface)) {
            throw new RegistryException('interface "' . $interface . '" does not exist.');
        }
        if (!is_subclass_of($interface, $parentInterface, true)) {
            throw new RegistryException('interface "' . $interface . '" has to extend "' . $parentInterface . '".');
        }
    }

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
        $schemaDocument->addCustomType($this->getValueSourceSchema(), ValueSourceSchema::TYPE);
        $schemaDocument->addCustomType($this->getValueModifierSchema(), ValueModifierSchema::TYPE);
        $schemaDocument->addCustomType(new ValueSchema(), ValueSchema::TYPE);
        $schemaDocument->addCustomType($this->getEvaluationSchema(), EvaluationSchema::TYPE);
        $schemaDocument->addCustomType($this->getComparisonSchema(), ComparisonSchema::TYPE);
        $schemaDocument->addCustomType($this->getDataMapperSchema(), DataMapperSchema::TYPE);

        foreach ($this->getIncludeValueSet() as $documentIdentifier => $label) {
            $schemaDocument->addValueToValueSet('document/all', $documentIdentifier, $label);
        }

        // TODO do we need these variations of the custom type "value"?
        // foreach ($this->getCustomValueSchemata() as $schema) {
        //     $schemaDocument->addCustomType($schema);
        // }

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

    public function getConfigurationSchema(): SchemaDocument
    {
        if (!isset($this->schemaDocument)) {
            $this->schemaDocument = new SchemaDocument();
            $this->addConfigurationSchema($this->schemaDocument);
        }
        return $this->schemaDocument;
    }
}
