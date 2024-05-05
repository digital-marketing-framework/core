<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class InheritableBooleanSchema extends StringSchema
{
    public const VALUE_INHERIT = 'inherit';

    public const VALUE_TRUE = 'yes';

    public const VALUE_FALSE = 'no';

    public function __construct(string $defaultValue = self::VALUE_INHERIT)
    {
        parent::__construct($defaultValue);
        $this->getAllowedValues()->addValue(static::VALUE_INHERIT);
        $this->getAllowedValues()->addValue(static::VALUE_TRUE);
        $this->getAllowedValues()->addValue(static::VALUE_FALSE);
        $this->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
    }

    /**
     * Converts a saved value (a string) to the actual (possibly inherited) boolean value
     * Will return null if the value is to be inherited, true or false otherwise.
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
