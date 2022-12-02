<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class GeneralEvaluation extends Evaluation implements GeneralConfigurationResolverInterface
{
    protected const KEY_THEN = 'then';
    protected const KEY_ELSE = 'else';

    protected mixed $then = null;
    protected mixed $else = null;

    public function __construct(string $keyword, RegistryInterface $registry, $config, ConfigurationResolverContextInterface $context)
    {
        parent::__construct($keyword, $registry, $config, $context);
        $this->initThenElseParts();
    }

    public static function negateEvaluationConfiguration(array $configuration): array
    {
        $negated = [
            'not' => $configuration,
        ];
        if (isset($configuration[static::KEY_THEN])) {
            $negated[static::KEY_THEN] = $configuration[static::KEY_THEN];
            unset($configuration[static::KEY_THEN]);
        }
        if (isset($configuration[static::KEY_ELSE])) {
            $negated[static::KEY_ELSE] = $configuration[static::KEY_ELSE];
            unset($configuration[static::KEY_ELSE]);
        }
        return $negated;
    }

    protected function initThenElseParts(): void
    {
        if (is_array($this->configuration)) {
            if (array_key_exists(static::KEY_THEN, $this->configuration)) {
                $this->then = $this->configuration[static::KEY_THEN];
                unset($this->configuration[static::KEY_THEN]);
            }
            if (array_key_exists(static::KEY_ELSE, $this->configuration)) {
                $this->else = $this->configuration[static::KEY_ELSE];
                unset($this->configuration[static::KEY_ELSE]);
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
