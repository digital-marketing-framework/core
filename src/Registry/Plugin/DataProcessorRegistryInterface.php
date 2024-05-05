<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\Condition\ConditionInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\DataMapperGroupInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

interface DataProcessorRegistryInterface extends PluginRegistryInterface
{
    public function getDataProcessor(): DataProcessorInterface;

    public function setDataProcessor(DataProcessorInterface $dataProcessor): void;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerValueSource(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteValueSource(string $keyword): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function getValueSource(string $keyword, array $configuration, DataProcessorContextInterface $context): ?ValueSourceInterface;

    public function getValueSourceSchema(): SchemaInterface;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerValueModifier(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteValueModifier(string $keyword): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function getValueModifier(string $keyword, array $configuration, DataProcessorContextInterface $context): ?ValueModifierInterface;

    public function getValueModifierSchema(): SchemaInterface;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerCondition(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteCondition(string $keyword): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function getCondition(string $keyword, array $configuration, DataProcessorContextInterface $context): ?ConditionInterface;

    public function getConditionSchema(): SchemaInterface;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerComparison(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteComparison(string $keyword): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function getComparison(string $keyword, array $configuration, DataProcessorContextInterface $context): ?ComparisonInterface;

    public function getComparisonSchema(): SchemaInterface;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerDataMapper(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteDataMapper(string $keyword): void;

    /**
     * @param array<string,mixed> $config
     */
    public function getDataMapper(string $keyword, array $config, DataProcessorContextInterface $context): ?DataMapperInterface;

    public function getDataMapperSchema(): SchemaInterface;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerDataMapperGroup(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteDataMapperGroup(string $keyword): void;

    /**
     * @param array<string,mixed> $config
     */
    public function getDataMapperGroup(string $keyword, array $config, DataProcessorContextInterface $context): ?DataMapperGroupInterface;

    public function getDataMapperGroupSchema(): SchemaInterface;
}
