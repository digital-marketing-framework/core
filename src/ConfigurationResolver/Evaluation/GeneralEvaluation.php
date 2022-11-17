<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class GeneralEvaluation extends Evaluation implements GeneralConfigurationResolverInterface
{
    protected mixed $then = null;
    
    protected mixed $else = null;

    public function __construct(string $keyword, RegistryInterface $registry, $config, ConfigurationResolverContextInterface $context)
    {
        parent::__construct($keyword, $registry, $config, $context);
        $this->initThenElseParts();
    }

    protected function initThenElseParts()
    {
        if (is_array($this->configuration)) {
            if (array_key_exists('then', $this->configuration)) {
                $this->then = $this->configuration['then'];
                unset($this->configuration['then']);
            }
            if (array_key_exists('else', $this->configuration)) {
                $this->else = $this->configuration['else'];
                unset($this->configuration['else']);
            }
        }
    }

    public function eval(): bool
    {
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('and', $this->configuration);
        return $evaluation->eval();
    }

    /**
     * the method "resolve" is calling "eval" and depending on its result
     * it will try to return a "then" or "else" part of the config.
     * if the needed part is missing in the config, it will return null
     */
    public function resolve(): mixed
    {
        $result = $this->eval();
        return $result ? $this->then : $this->else;
    }
}
