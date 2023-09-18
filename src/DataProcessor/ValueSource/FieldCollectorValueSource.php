<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class FieldCollectorValueSource extends ValueSource
{
    public const WEIGHT = 20;

    public const KEY_IGNORE_IF_EMPTY = 'ignoreIfEmpty';

    public const DEFAULT_IGNORE_IF_EMPTY = true;

    public const KEY_UNPROCESSED_ONLY = 'unprocessedOnly';

    public const DEFAULT_UNPROCESSED_ONLY = true;

    public const KEY_EXCLUDE = 'exclude';

    public const DEFAULT_EXCLUDE = '';

    public const KEY_INCLUDE = 'include';

    public const DEFAULT_INCLUDE = '';

    public const KEY_TEMPLATE = 'template';

    public const DEFAULT_TEMPLATE = '{key}\\s=\\s{value}\\n';

    protected bool $ignoreIfEmpty;

    protected bool $unprocessedOnly;

    /** @var array<string> */
    protected array $excludeFields = [];

    /** @var array<string> */
    protected array $includeFields = [];

    protected string $template;

    protected function getMultiValue(): MultiValueInterface
    {
        return new MultiValue(glue: '');
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
        return !($this->unprocessedOnly && $this->context->getFieldTracker()->hasBeenProcessed($key));
    }

    protected function addValue(MultiValueInterface $output, string|int $key, string|ValueInterface $value): void
    {
        $output[] = $value;
    }

    protected function processField(string|int $key, string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        return match ($this->template) {
            '{key}' => $key,
            '{value}' => $value,
            default => str_replace(['{key}', '{value}'], [$key, $value], $this->template),
        };
    }

    public function build(): null|string|ValueInterface
    {
        $this->ignoreIfEmpty = $this->getConfig(static::KEY_IGNORE_IF_EMPTY);
        $this->unprocessedOnly = $this->getConfig(static::KEY_UNPROCESSED_ONLY);
        $this->excludeFields = GeneralUtility::castValueToArray($this->getConfig(static::KEY_EXCLUDE));
        $this->includeFields = GeneralUtility::castValueToArray($this->getConfig(static::KEY_INCLUDE));
        $this->template = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_TEMPLATE));

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

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_IGNORE_IF_EMPTY, new BooleanSchema(static::DEFAULT_IGNORE_IF_EMPTY));
        $schema->addProperty(static::KEY_UNPROCESSED_ONLY, new BooleanSchema(static::DEFAULT_UNPROCESSED_ONLY));
        $schema->addProperty(static::KEY_EXCLUDE, new StringSchema());
        $schema->addProperty(static::KEY_INCLUDE, new StringSchema());
        $schema->addProperty(static::KEY_TEMPLATE, new StringSchema(static::DEFAULT_TEMPLATE));

        return $schema;
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}
