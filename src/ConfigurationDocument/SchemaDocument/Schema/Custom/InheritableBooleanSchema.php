<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;

class InheritableBooleanSchema extends StringSchema
{
    public const VALUE_INHERIT = 'inherit';
    public const VALUE_TRUE = 'yes';
    public const VALUE_FALSE = 'no';

    public function __construct()
    {
        parent::__construct();
        $this->getAllowedValues()->addValue(static::VALUE_INHERIT);
        $this->getAllowedValues()->addValue(static::VALUE_TRUE);
        $this->getAllowedValues()->addValue(static::VALUE_FALSE);
        $this->setDefaultValue(static::VALUE_INHERIT);
        $this->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
    }

    /**
     * Converts a saved value (a string) to the actual (possibly inherited) boolean value
     * Will return null if the value is to be inherited, true or false otherwise.
     */
    public static function convert(string $value): ?bool
    {
        switch ($value) {
            case static::VALUE_TRUE:
                return true;
            case static::VALUE_FALSE:
                return false;
            default:
                return null;
        }
    }
}
