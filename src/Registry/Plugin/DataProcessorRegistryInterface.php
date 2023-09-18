<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorInterface;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;

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
    public function registerEvaluation(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteEvaluation(string $keyword): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function getEvaluation(string $keyword, array $configuration, DataProcessorContextInterface $context): ?EvaluationInterface;

    public function getEvaluationSchema(): SchemaInterface;

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
}
