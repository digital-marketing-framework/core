<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\FalseEvaluation;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\TrueEvaluation;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DataProcessor extends Plugin implements DataProcessorInterface
{
    public const KEY_TYPE = 'type';
    public const KEY_DATA = 'data';
    public const KEY_CONFIG = 'config';
    public const KEY_MODIFIERS = 'modifiers';

    public function __construct(
        protected RegistryInterface $registry
    ) {
    }

    protected function getType(array $config): string
    {
        if (!isset($config[static::KEY_TYPE])) {
            throw new DigitalMarketingFrameworkException('No type given.');
        }
        return $config[static::KEY_TYPE];
    }

    protected function getConfig(array $config, string $keyword): array
    {
        return $config[static::KEY_CONFIG][$keyword] ?? [];
    }

    public function createContext(DataInterface $data, ConfigurationInterface $configuration): DataProcessorContextInterface
    {
        return new DataProcessorContext($data, $configuration);
    }
    
    public function processValueSource(array $config, DataProcessorContextInterface $context): string|ValueInterface|null
    {
        $keyword = $this->getType($config);
        $valueSourceConfig = $this->getConfig($config, $keyword);
        $valueSource = $this->registry->getValueSource($keyword, $valueSourceConfig, $context);
        if ($valueSource === null) {
            throw new DigitalMarketingFrameworkException(sprintf('ValueSource "%s" not found.', $keyword));
        }
        return $valueSource->build($context);
    }

    public function processValueModifiers(array $config, string|ValueInterface|null $value, DataProcessorContextInterface $context): string|ValueInterface|null
    {
        foreach ($config as $keyword => $modifierConfig) {
            $modifier = $this->registry->getValueModifier($keyword, $modifierConfig, $context);
            if ($modifier === null) {
                throw new DigitalMarketingFrameworkException(sprintf('ValueModifier "%s" not found.', $keyword));
            }
            $value = $modifier->modify($value);
        }
        return $value;
    }

    public function processValue(array $config, DataProcessorContextInterface $context): string|ValueInterface|null
    {
        // build
        $dataConfig = $config[static::KEY_DATA] ?? null;
        if ($dataConfig === null) {
            throw new DigitalMarketingFrameworkException('No data for value source configuration found.');
        }
        $value = $this->processValueSource($dataConfig, $context);

        // modify
        $modifierConfig = $config[static::KEY_MODIFIERS] ?? null;
        if ($modifierConfig === null) {
            throw new DigitalMarketingFrameworkException('No data for value modifiers configuration found.');
        }
        $value = $this->processValueModifiers($config[static::KEY_MODIFIERS], $value, $context);

        return $value;
    }

    public function processComparison(array $config, DataProcessorContextInterface $context): bool
    {
        $keyword = $this->getType($config);
        $comparison = $this->registry->getComparison($keyword, $config, $context);
        if ($comparison === null) {
            throw new DigitalMarketingFrameworkException(sprintf('Comparison "%s" not found.', $keyword));
        }
        return $comparison->compare();
    }

    public function processEvaluation(array $config, DataProcessorContextInterface $context): bool
    {
        $keyword = $this->getType($config);
        $evaluationConfig = $this->getConfig($config, $keyword);
        $evaluation = $this->registry->getEvaluation($keyword, $evaluationConfig, $context);
        if ($evaluation === null) {
            throw new DigitalMarketingFrameworkException(sprintf('Evaluation "%s" not found.', $keyword));
        }
        return $evaluation->evaluate();
    }

    public function processDataMapper(array $config, DataInterface $data, ConfigurationInterface $configuration): DataInterface
    {
        $context = $this->createContext($data, $configuration);
        $target = new Data();
        foreach ($config as $keyword => $dataMapperConfig) {
            $dataMapper = $this->registry->getDataMapper($keyword, $dataMapperConfig, $context);
            if ($dataMapper === null) {
                throw new DigitalMarketingFrameworkException(sprintf('DataMapper "%s" not found.', $keyword));
            }
            $target = $dataMapper->mapData($target);
        }
        return $target;
    }

    public static function getDefaultValueSourceConfiguration(string $class = ConstantValueSource::class, ?string $keyword = null, ?array $config = null): array
    {
        if ($keyword === null) {
            $keyword = GeneralUtility::getPluginKeyword($class, ValueSourceInterface::class);
        }
        return [
            static::KEY_TYPE => $keyword,
            static::KEY_CONFIG => [
                $keyword => $config ?? $class::getDefaultConfiguration(),
            ],
        ];
    }

    public static function getDefaultValueConfiguration(string $sourceClass = ConstantValueSource::class, ?string $sourceKeyword = null, ?array $sourceConfig = null): array
    {
        return [
            static::KEY_DATA => static::getDefaultValueSourceConfiguration($sourceClass, $sourceKeyword, $sourceConfig),
            static::KEY_MODIFIERS => [],
        ];
    }

    public static function getDefaultEvaluationConfiguration(bool $true = true): array
    {
        $keyword = $true ? 'true' : 'false';
        return [
            static::KEY_TYPE => $keyword,
            static::KEY_CONFIG => [
                $keyword => $true 
                    ? TrueEvaluation::getDefaultConfiguration()
                    : FalseEvaluation::getDefaultConfiguration(),
            ]
        ];
    }

    public static function getDefaultDataMapperConfiguration(): array
    {
        return [
            'passthroughFields' => PassthroughFieldsDataMapper::getDefaultConfiguration(true),
        ];
    }
}
