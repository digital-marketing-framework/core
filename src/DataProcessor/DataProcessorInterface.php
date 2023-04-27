<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface DataProcessorInterface extends PluginInterface
{
    public function createContext(DataInterface $data, ConfigurationInterface $configuration): DataProcessorContextInterface;
    
    public function processValueSource(array $config, DataProcessorContextInterface $context): string|ValueInterface|null;
    public function processValueModifiers(array $config, string|ValueInterface|null $value, DataProcessorContextInterface $context): string|ValueInterface|null;
    public function processValue(array $config, DataProcessorContextInterface $context): string|ValueInterface|null;
    public function processEvaluation(array $config, DataProcessorContextInterface $context): bool;
    public function processComparison(array $config, DataProcessorContextInterface $context): bool;
    public function processDataMapper(array $config, DataInterface $data, ConfigurationInterface $configuration): DataInterface;

    public static function getDefaultValueConfiguration(): array;
    public static function getDefaultEvaluationConfiguration(bool $true = true): array;
    public static function getDefaultDataMapperConfiguration(): array;
}
