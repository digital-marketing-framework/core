<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\Service\DataProcessorInterface;

interface ConfigurationResolverRegistryInterface extends PluginRegistryInterface
{
    public function getDataProcessor(array $configuration): DataProcessorInterface;

    public function getConfigurationResolver(string $keyword, string $interface, mixed $config, ConfigurationResolverContextInterface $context): ?ConfigurationResolverInterface;

    public function registerContentResolver(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteContentResolver(string $keyword): void;
    
    public function getContentResolver(string $keyword, mixed $config, ConfigurationResolverContextInterface $context): ?ContentResolverInterface;
    
    public function registerEvaluation(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteEvaluation(string $keyword): void;

    public function getEvaluation(string $keyword, mixed $config, ConfigurationResolverContextInterface $context): ?EvaluationInterface;
}
