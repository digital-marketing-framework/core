<?php

namespace DigitalMarketingFramework\Core\Helper;

use BadMethodCallException;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;

trait ConfigurationResolverTrait
{
    protected function getConfigurationResolverContext(): ConfigurationResolverContextInterface
    {
        throw new BadMethodCallException(sprintf('Class "%s" does not implement required method "getConfigurationResolverContext".', get_class($this)));
    }

    protected function getConfigurationResolver(string $resolverInterface, string $keyword, mixed $config, ?ConfigurationResolverContextInterface $context = null): ?ConfigurationResolverInterface
    {
        if ($context === null) {
            $context = $this->getConfigurationResolverContext();
        }
        return $this->registry->getConfigurationResolver($keyword, $resolverInterface, $config, $context);
    }

    protected function getContentResolver(mixed $config, string $keyword = 'general', ?ConfigurationResolverContextInterface $context = null): ?ContentResolverInterface
    {
        if ($context === null) {
            $context = $this->getConfigurationResolverContext();
        }
        return $this->registry->getContentResolver($keyword, $config, $context);
    }

    protected function getEvaluation(mixed $config, string $keyword = 'general', ?ConfigurationResolverContextInterface $context = null): ?EvaluationInterface
    {
        if ($context === null) {
            $context = $this->getConfigurationResolverContext();
        }
        return $this->registry->getEvaluation($keyword, $config, $context);
    }

    protected function resolveContent(mixed $config, ?ConfigurationResolverContextInterface $context = null): mixed
    {
        /** @var ?GeneralContentResolver $contentResolver */
        $contentResolver = $this->getContentResolver($config, 'general', $context);
        return $contentResolver?->resolve();
    }

    protected function resolveEvaluation(mixed $config, ?ConfigurationResolverContextInterface $context = null): mixed
    {
        /** @var ?GeneralEvaluation */
        $evaluation = $this->getEvaluation($config, 'general', $context);
        return $evaluation?->resolve();
    }

    protected function evaluate(mixed $config, ?ConfigurationResolverContextInterface $context = null): mixed
    {
        /** @var GeneralEvaluation */
        $evaluation = $this->getEvaluation($config, 'general', $context);
        return $evaluation?->eval() ?? false;
    }
}
