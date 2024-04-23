<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\BinaryComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorInterface;
use DigitalMarketingFramework\Core\DataProcessor\Condition\ConditionInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\DataMapperGroupInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ConditionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperGroupSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

trait DataProcessorRegistryTrait
{
    use PluginRegistryTrait;

    protected DataProcessorInterface $dataProcessor;

    abstract public function createObject(string $class, array $arguments = []): object;

    public function getDataProcessor(): DataProcessorInterface
    {
        if (!isset($this->dataProcessor)) {
            $dataProcessor = $this->createObject(DataProcessor::class, [$this]);
            $this->dataProcessor = $dataProcessor;
        }

        return $this->dataProcessor;
    }

    public function setDataProcessor(DataProcessorInterface $dataProcessor): void
    {
        $this->dataProcessor = $dataProcessor;
    }

    // -- ValueSource --

    public function registerValueSource(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(ValueSourceInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteValueSource(string $keyword): void
    {
        $this->deletePlugin($keyword, ValueSourceInterface::class);
    }

    public function getValueSource(string $keyword, array $config, DataProcessorContextInterface $context): ?ValueSourceInterface
    {
        return $this->getPlugin($keyword, ValueSourceInterface::class, [$config, $context]);
    }

    public function getValueSourceSchema(): SchemaInterface
    {
        $schema = new ValueSourceSchema();
        foreach ($this->getAllPluginClasses(ValueSourceInterface::class) as $key => $class) {
            $schema->addItem($key, $class::getSchema());
            if ($class::modifiable()) {
                $schema->addModifiableKeyword($key);
            }

            if ($class::canBeMultiValue()) {
                $schema->addCanBeMultiValueKeyword($key);
            }
        }

        return $schema;
    }

    // -- ValueModifier --

    public function registerValueModifier(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(ValueModifierInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteValueModifier(string $keyword): void
    {
        $this->deletePlugin($keyword, ValueModifierInterface::class);
    }

    public function getValueModifier(string $keyword, array $config, DataProcessorContextInterface $context): ?ValueModifierInterface
    {
        return $this->getPlugin($keyword, ValueModifierInterface::class, [$config, $context]);
    }

    public function getValueModifierSchema(): SchemaInterface
    {
        $schema = new ValueModifierSchema();
        foreach ($this->getAllPluginClasses(ValueModifierInterface::class) as $key => $class) {
            $schema->addItem($key, $class::getSchema());
        }

        return $schema;
    }

    // -- Condition --

    public function registerCondition(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(ConditionInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteCondition(string $keyword): void
    {
        $this->deletePlugin($keyword, ConditionInterface::class);
    }

    public function getCondition(string $keyword, array $config, DataProcessorContextInterface $context): ?ConditionInterface
    {
        return $this->getPlugin($keyword, ConditionInterface::class, [$config, $context]);
    }

    public function getConditionSchema(bool $withContext = false): SchemaInterface
    {
        $schema = new ConditionSchema(withContext: $withContext);
        foreach ($this->getAllPluginClasses(ConditionInterface::class) as $key => $class) {
            $schema->addItem($key, $class::getSchema());
        }

        return $schema;
    }

    // -- Comparison --

    public function registerComparison(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(ComparisonInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteComparison(string $keyword): void
    {
        $this->deletePlugin($keyword, ComparisonInterface::class);
    }

    public function getComparison(string $keyword, array $config, DataProcessorContextInterface $context): ?ComparisonInterface
    {
        return $this->getPlugin($keyword, ComparisonInterface::class, [$config, $context]);
    }

    public function getComparisonSchema(): SchemaInterface
    {
        $schema = new ComparisonSchema();
        foreach ($this->getAllPluginClasses(ComparisonInterface::class) as $key => $class) {
            $binaryOperation = is_a($class, BinaryComparison::class, true);
            $multiValueHandlingOperation = $class::handleMultiValuesIndividually();
            $schema->addValueToValueSet(ComparisonSchema::VALUE_SET_ALL, $key);
            if ($binaryOperation) {
                $schema->addValueToValueSet(ComparisonSchema::VALUE_SET_BINARY_OPERATIONS, $key);
            }

            if ($multiValueHandlingOperation) {
                $schema->addValueToValueSet(ComparisonSchema::VALUE_SET_MULTI_VALUE_HANDLING_OPERATIONS, $key);
            }
        }

        return $schema;
    }

    // -- DataMapper --

    public function registerDataMapper(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(DataMapperInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteDataMapper(string $keyword): void
    {
        $this->deletePlugin($keyword, DataMapperInterface::class);
    }

    public function getDataMapper(string $keyword, array $config, DataProcessorContextInterface $context): ?DataMapperInterface
    {
        return $this->getPlugin($keyword, DataMapperInterface::class, [$config, $context]);
    }

    public function getDataMapperSchema(): SchemaInterface
    {
        $schema = new DataMapperSchema();
        foreach ($this->getAllPluginClasses(DataMapperInterface::class) as $key => $class) {
            $schema->addItem($key, $class::getSchema());
        }

        return $schema;
    }

    // -- DataMapperGroup --

    public function registerDataMapperGroup(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(DataMapperGroupInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteDataMapperGroup(string $keyword): void
    {
        $this->deletePlugin($keyword, DataMapperGroupInterface::class);
    }

    public function getDataMapperGroup(string $keyword, array $config, DataProcessorContextInterface $context): ?DataMapperGroupInterface
    {
        return $this->getPlugin($keyword, DataMapperGroupInterface::class, [$config, $context]);
    }

    public function getDataMapperGroupSchema(): SchemaInterface
    {
        $schema = new DataMapperGroupSchema();
        foreach ($this->getAllPluginClasses(DataMapperGroupInterface::class) as $key => $class) {
            $schema->addItem($key, $class::getSchema(), $class::getLabel());
        }

        return $schema;
    }
}
