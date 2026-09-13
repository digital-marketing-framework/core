<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

/**
 * A boolean that can also be left undefined.
 *
 * The third state has its own keyword rather than being an empty string, so a saved value
 * says what it means. The keyword says only that no value was given; what that implies is
 * the caller's business, and convert() reports it as null either way. A subclass names the
 * state after whatever it means there — see InheritableBooleanSchema, where it means "take
 * the value from somewhere else in this configuration".
 */
class NullableBooleanSchema extends StringSchema
{
    public const VALUE_TRUE = 'yes';

    public const VALUE_FALSE = 'no';

    public const VALUE_NULL = 'undefined';

    public function __construct(string $defaultValue = self::VALUE_NULL)
    {
        parent::__construct($defaultValue);
        // No explicit labels: the value set derives them from the keywords, which is also
        // what a subclass renaming VALUE_NULL wants.
        $this->getAllowedValues()->addValue(static::VALUE_NULL);
        $this->getAllowedValues()->addValue(static::VALUE_TRUE);
        $this->getAllowedValues()->addValue(static::VALUE_FALSE);
        $this->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
    }

    /**
     * Converts a saved value to the boolean it stands for, or null when none was specified.
     */
    public static function convert(string $value): ?bool
    {
        return match ($value) {
            static::VALUE_TRUE => true,
            static::VALUE_FALSE => false,
            default => null,
        };
    }
}
