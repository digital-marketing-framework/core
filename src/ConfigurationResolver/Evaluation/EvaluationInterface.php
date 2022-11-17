<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

interface EvaluationInterface extends ConfigurationResolverInterface
{
    public function eval(): bool;
}
