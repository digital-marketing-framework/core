<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class AbstractFieldCollectorContentResolver extends ContentResolver
{
    protected const KEY_EXCLUDE = 'exclude';
    protected const DEFAULT_EXCLUDE = '';

    protected const KEY_INCLUDE = 'include';
    protected const DEFAULT_INCLUDE = '';

    protected const KEY_IGNORE_IF_EMPTY = 'ignoreIfEmpty';
    protected const DEFAULT_IGNORE_IF_EMPTY = true;

    protected const KEY_UNPROCESSED_ONLY = 'unprocessedOnly';
    protected const DEFAULT_UNPROCESSED_ONLY = true;

    protected array $excludeFields;
    protected array $includeFields;
    protected bool $ignoreIfEmpty;
    protected bool $unprocessedOnly;

    public function __construct(string $keyword, ConfigurationResolverRegistryInterface $registry, $config, ConfigurationResolverContextInterface $context)
    {
        parent::__construct($keyword, $registry, $config, $context);
        $this->excludeFields = GeneralUtility::castValueToArray($this->resolveContent($this->getConfig(static::KEY_EXCLUDE)));
        $this->includeFields = GeneralUtility::castValueToArray($this->resolveContent($this->getConfig(static::KEY_INCLUDE)));
        $this->ignoreIfEmpty = $this->evaluate($this->getConfig(static::KEY_IGNORE_IF_EMPTY));
        $this->unprocessedOnly = $this->evaluate($this->getConfig(static::KEY_UNPROCESSED_ONLY));
    }

    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::IgnoreScalar;
    }

    protected function getMultiValue(): MultiValueInterface
    {
        return new MultiValue();
    }

    abstract protected function processField(string|int $key, string|ValueInterface|null $value): string|ValueInterface|null;

    protected function addValue(MultiValueInterface $output, string|int $key, string|ValueInterface|null $value): void
    {
        $output[$key] = $value;
    }

    protected function allowField(string|int $key, string|ValueInterface|null $value): bool
    {
        // null values are always ignored
        if ($value === null) {
            return false;
        }

        // exclude settings have the highest priority, even above include settings
        if (in_array($key, $this->excludeFields)) {
            return false;
        }

        // empty fields being ignored is always taken into account
        if ($this->ignoreIfEmpty && GeneralUtility::isEmpty($value)) {
            return false;
        }

        // include fields are ranked higher than "unprocessedOnly" filter
        // in fact, incude fields only exist to add fields specifically in addition to the unprocessed ones
        if (in_array($key, $this->includeFields) || in_array('*', $this->includeFields)) {
            return true;
        }

        // unprocessedOnly has lowest priority because it is the most indirect directive
        if ($this->unprocessedOnly && $this->context->getFieldTracker()->hasBeenProcessed($key)) {
            return false;
        }

        // allow a field by default
        return true;
    }

    public function build(): string|ValueInterface|null
    {
        $result = $this->getMultiValue();
        foreach ($this->context->getData() as $key => $value) {
            if (!$this->allowField($key, $value)) {
                continue;
            }

            $processedValue = $this->processField($key, $value);
            if ($processedValue !== null) {
                $this->addValue($result, $key, $processedValue);
            }
        }
        return $result;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_EXCLUDE => static::DEFAULT_EXCLUDE,
            static::KEY_INCLUDE => static::DEFAULT_INCLUDE,
            static::KEY_IGNORE_IF_EMPTY => static::DEFAULT_IGNORE_IF_EMPTY,
            static::KEY_UNPROCESSED_ONLY => static::DEFAULT_UNPROCESSED_ONLY,
        ];
    }
}
