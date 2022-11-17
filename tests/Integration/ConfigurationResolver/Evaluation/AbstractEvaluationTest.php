<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

abstract class AbstractEvaluationTest extends AbstractConfigurationResolverTest
{
    protected bool $eval = false;

    protected function getGeneralResolverClass(): string
    {
        return GeneralEvaluation::class;
    }

    protected function executeResolver(GeneralConfigurationResolverInterface $resolver): mixed
    {
        if ($this->eval) {
            /** @var GeneralEvaluation $resolver */
            return $resolver->eval();
        } else {
            return parent::executeResolver($resolver);
        }
    }

    protected function runResolverProcess(mixed $config): mixed
    {
        $this->eval = false;
        return parent::runResolverProcess($config);
    }

    protected function runEvaluationProcess(mixed $config): bool
    {
        $this->eval = true;
        return parent::runResolverProcess($config);
    }
}
