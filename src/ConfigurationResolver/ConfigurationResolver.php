<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\Helper\ConfigurationResolverTrait;
use DigitalMarketingFramework\Core\Helper\ConfigurationTrait;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class ConfigurationResolver extends Plugin implements ConfigurationResolverInterface
{
    use ConfigurationTrait;
    use ConfigurationResolverTrait;

    protected const KEY_WEIGHT = 'weight';

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

    protected function getConfigurationResolverContext(): ConfigurationResolverContextInterface
    {
        return $this->context->copy();
    }

    protected function resolveKeyword(string $keyword, mixed $config, ?ConfigurationResolverContextInterface $context = null): ?ConfigurationResolverInterface
    {
        return $this->getConfigurationResolver(static::getResolverInterface(), $keyword, $config, $context);
    }

    abstract protected static function getResolverInterface(): string;

    protected function sortSubResolvers(array &$subResolvers): void
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

    protected function getFieldValue(string $key, bool $markAsProcessed = true): string|ValueInterface|null
    {
        $fieldValue = $this->fieldExists($key, $markAsProcessed)
            ? $this->context->getData()[$key]
            : null;
        return $fieldValue;
    }

    protected function addKeyToContext(mixed $key, ?ConfigurationResolverContextInterface $context = null): void
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

    protected function addIndexToContext(mixed $index, ?ConfigurationResolverContextInterface $context = null): void
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
    protected function getKeyFromContext(?ConfigurationResolverContextInterface $context = null): string
    {
        if ($context === null) {
            $context = $this->context;
        }
        return isset($context['key']) ? (string)$context['key'] : '';
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
     */
    protected function getSelectedValue(?ConfigurationResolverContextInterface $context = null): string|ValueInterface|null
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
                    if ($fieldValue instanceof MultiValueInterface) {
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

    public function getWeight(): int
    {
        return $this->getConfig(static::KEY_WEIGHT);
    }

    public static function getDefaultConfiguration(): array
    {
        return [
            static::KEY_WEIGHT => static::WEIGHT,
        ];
    }
}
