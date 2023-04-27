<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\EvaluationSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorInterface;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;

trait DataProcessorRegistryTrait
{
    use PluginRegistryTrait;

    protected DataProcessorInterface $dataProcessor;

    abstract protected function createObject(string $class, array $arguments = []): object;

    public function getDataProcessor(): DataProcessorInterface
    {
        if (!isset($this->dataProcessor)) {
            $this->dataProcessor = $this->createObject(DataProcessor::class, [$this]);
        }
        return $this->dataProcessor;
    }

    public function setDataProcessor(DataProcessorInterface $dataProcessor): void
    {
        $this->dataProcessor = $dataProcessor;
    }

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
        return new ValueSourceSchema($this);
    }

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
        return new ValueModifierSchema($this);
    }

    /**
     * @return array<ValueSchema>
     */
    public function getCustomValueSchemata(): array
    {
        $schemata = [];
        foreach (array_keys($this->getAllPluginClasses(ValueSourceInterface::class)) as $keyword) {
            $schemata[] = new ValueSchema($this, $keyword, false);
            $schemata[] = new ValueSchema($this, $keyword, true);
        }
        return $schemata;
    }
    
    public function registerEvaluation(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(EvaluationInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteEvaluation(string $keyword): void
    {
        $this->deletePlugin($keyword, EvaluationInterface::class);
    }

    public function getEvaluation(string $keyword, array $config, DataProcessorContextInterface $context): ?EvaluationInterface
    {
        return $this->getPlugin($keyword, EvaluationInterface::class, [$config, $context]);
    }

    public function getEvaluationSchema(): SchemaInterface
    {
        return new EvaluationSchema($this);
    }

    public function registerComparison(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(ComparisonInterface::class, $additionalArguments, $keyword);
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
        return new ComparisonSchema($this);
    }

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
        return new DataMapperSchema($this);
    }
}
