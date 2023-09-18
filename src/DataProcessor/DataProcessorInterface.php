<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface DataProcessorInterface extends PluginInterface
{
    public function createContext(DataInterface $data, ConfigurationInterface $configuration): DataProcessorContextInterface;

    /**
     * @param array<string,mixed> $config
     */
    public function processValueSource(array $config, DataProcessorContextInterface $context): string|ValueInterface|null;

    /**
     * @param array<string,mixed> $config
     */
    public function processValueModifiers(array $config, string|ValueInterface|null $value, DataProcessorContextInterface $context): string|ValueInterface|null;

    /**
     * @param array<string,mixed> $config
     */
    public function processValue(array $config, DataProcessorContextInterface $context): string|ValueInterface|null;

    /**
     * @param array<string,mixed> $config
     */
    public function processEvaluation(array $config, DataProcessorContextInterface $context): bool;

    /**
     * @param array<string,mixed> $config
     */
    public function processComparison(array $config, DataProcessorContextInterface $context): bool;

    /**
     * @param array<string,mixed> $config
     */
    public function processDataMapper(array $config, DataInterface $data, ConfigurationInterface $configuration): DataInterface;
}
