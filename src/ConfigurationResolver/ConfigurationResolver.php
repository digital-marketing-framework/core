<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use DigitalMarketingFramework\Core\Helper\ConfigurationTrait;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class ConfigurationResolver extends Plugin implements ConfigurationResolverInterface
{
    use ConfigurationTrait;

    protected const KEY_WEIGHT = 'weight';
    protected const DEFAULT_WEIGHT = null;

    protected ConfigurationResolverRegistryInterface $registry;

    protected mixed $configuration;

    protected ConfigurationResolverContextInterface $context;

    /**
     * @param array|string $config
     */
    public function __construct(string $keyword, ConfigurationResolverRegistryInterface $registry, $config, ConfigurationResolverContextInterface $context)
    {
        parent::__construct($keyword);
        $this->registry = $registry;
        $this->context = $context;
        $this->configuration = $config;

        switch ($this->getConfigurationBehaviour()) {
            case ConfigurationBehaviour::IgnoreScalar:
                if (!is_array($config)) {
                    $this->configuration = [];
                }
                break;
            case ConfigurationBehaviour::ResolveContentThenCastToArray:
                $this->configuration = GeneralUtility::castValueToArray(
                    $this->resolveContent($this->configuration)
                );
                break;
            case ConfigurationBehaviour::ConvertScalarToArrayWithSelfValue:
                if (!is_array($config)) {
                    $this->configuration = [static::KEY_SELF => $this->configuration];
                }
                break;
        }
    }

    protected function resolveForeignKeyword(string $resolverInterface, string $keyword, $config, ?ConfigurationResolverContextInterface $context = null)
    {
        if ($context === null) {
            $context = $this->context->copy();
        }
        return $this->registry->getConfigurationResolver($keyword, $resolverInterface, $config, $context);
    }

    /**
     * @param string $keyword
     * @param array|string $config
     * @param ConfigurationResolverContextInterface $context
     * @return ConfigurationResolverInterface|null
     */
    protected function resolveKeyword(string $keyword, $config, ConfigurationResolverContextInterface $context = null)
    {
        return $this->resolveForeignKeyword(static::getResolverInterface(), $keyword, $config, $context);
    }

    abstract protected static function getResolverInterface(): string;

    protected function sortSubResolvers(array &$subResolvers)
    {
        ksort($subResolvers, SORT_NUMERIC);
        usort($subResolvers, function (ConfigurationResolverInterface $a, ConfigurationResolverInterface $b) {
            return $a->getWeight() <=> $b->getWeight();
        });
    }

    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::Default;
    }

    protected function fieldExists($key, bool $markAsProcessed = true): bool
    {
        if ($markAsProcessed) {
            $this->context->getFieldTracker()->markAsProcessed($key);
        }
        return $this->context->getData()->fieldExists($key);
    }

    protected function getFieldValue($key, bool $markAsProcessed = true)
    {
        $fieldValue = $this->fieldExists($key, $markAsProcessed)
            ? $this->context->getData()[$key]
            : null;
        return $fieldValue;
    }

    protected function addKeyToContext(mixed $key, ?ConfigurationResolverContextInterface $context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        $resolvedKey = $this->resolveContent($key);
        if (!GeneralUtility::isEmpty($resolvedKey)) {
            $context['key'] = $resolvedKey;
        } elseif (isset($context['key'])) {
            unset($context['key']);
        }
        if (isset($context['index'])) {
            unset($context['index']);
        }
    }

    protected function addIndexToContext(mixed $index, ?ConfigurationResolverContextInterface $context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        $resolvedKey = $this->resolveContent($index);
        if (!GeneralUtility::isEmpty($resolvedKey)) {
            $index = $this->getIndexFromContext($context);
            $index[] = $resolvedKey;
            $context['index'] = $index;
        } elseif (isset($context['index'])) {
            unset($context['index']);
        }
    }

    /**
     * @param ConfigurationResolverContextInterface|null $context
     * @return ValueInterface|string|null
     */
    protected function getKeyFromContext($context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        return $context['key'] ?? '';
    }

    /**
     * @param ConfigurationResolverContextInterface|null $context
     * @return array
     */
    protected function getIndexFromContext($context = null): array
    {
        if ($context === null) {
            $context = $this->context;
        }
        return $context['index'] ?? [];
    }

    /**
     * Fetching the value of the previously selected field (and index).
     * Examples:
     * field.country // === getFieldValue(country)
     * field.countries.index.0 // === getFieldValue(countries)[0]
     * field.some_deep_nested_field.index.7.index.5 // === getFieldValue(some_deep_nested_field)[7][5]
     *
     * Can also just return the field name instead of its value. In such a case the index is ignored.
     * Examples:
     * field.country.key = country // a tautology
     * loopData.condition.key.in = country,state // loops over the fields "country" and "state" if they exist
     *
     * @param ConfigurationResolverContextInterface|null $context
     * @return ValueInterface|string|null
     */
    protected function getSelectedValue($context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        $key = $this->getKeyFromContext();
        if ($key) {
            if ($context['useKey'] ?? false) {
                return $key;
            } else {
                $fieldValue = $this->getFieldValue($key);
                $indices = $this->getIndexFromContext($context);
                while (!empty($indices)) {
                    $index = array_shift($indices);
                    if ($fieldValue instanceof MultiValue) {
                        $fieldValue = $fieldValue[$index] ?? null;
                    } else {
                        $fieldValue = null;
                        break;
                    }
                }
                return $fieldValue;
            }
        }
        return null;
    }

    protected function resolveContent($config, ConfigurationResolverContextInterface $context = null)
    {
        /** @var GeneralContentResolver $contentResolver */
        $contentResolver = $this->resolveForeignKeyword(ContentResolverInterface::class, 'general', $config, $context);
        return $contentResolver->resolve();
    }

    protected function resolveValueMap($config, $value, ConfigurationResolverContextInterface $context = null)
    {
        /** @var GeneralValueMapper $valueMapper */
        $valueMapper = $this->resolveForeignKeyword(ValueMapperInterface::class, 'general', $config, $context);
        return $valueMapper->resolve($value);
    }

    protected function resolveEvaluation($config, ConfigurationResolverContextInterface $context = null)
    {
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->resolveForeignKeyword(EvaluationInterface::class, 'general', $config, $context);
        return $evaluation->resolve();
    }

    protected function evaluate($config, ConfigurationResolverContextInterface $context = null)
    {
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->resolveForeignKeyword(EvaluationInterface::class, 'general', $config, $context);
        return $evaluation->eval();
    }

    public function getWeight(): int
    {
        return $this->getConfig(static::KEY_WEIGHT) ?? static::WEIGHT;
    }

    public static function getDefaultConfiguration(): array
    {
        return [];
    }
}
