<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\Service\DataProcessor;
use DigitalMarketingFramework\Core\Service\DataProcessorInterface;

trait ConfigurationResolverRegistryTrait
{
    use PluginRegistryTrait;

    abstract protected function createObject(string $class, array $arguments = []): object;

    public function getDataProcessor(array $configuration): DataProcessorInterface
    {
        return $this->createObject(DataProcessor::class, [$this, $configuration]);
    }

    public function getConfigurationResolver(string $keyword, string $interface, mixed $config, ConfigurationResolverContextInterface $context): ?ConfigurationResolverInterface
    {
        $this->interfaceValidation($interface, ConfigurationResolverInterface::class);
        return $this->getPlugin($keyword, $interface, [$config, $context]);
    }

    public function registerContentResolver(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(ContentResolverInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteContentResolver(string $keyword): void
    {
        $this->deletePlugin($keyword, ContentResolverInterface::class);
    }
    
    public function getContentResolver(string $keyword, mixed $config, ConfigurationResolverContextInterface $context): ?ContentResolverInterface
    {
        return $this->getConfigurationResolver($keyword, ContentResolverInterface::class, $config, $context);
    }
    
    public function registerEvaluation(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(EvaluationInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteEvaluation(string $keyword): void
    {
        $this->deletePlugin($keyword, EvaluationInterface::class);
    }

    public function getEvaluation(string $keyword, mixed $config, ConfigurationResolverContextInterface $context): ?EvaluationInterface
    {
        return $this->getConfigurationResolver($keyword, EvaluationInterface::class, $config, $context);
    }
}
