<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class NegateValueModifier extends ValueModifier
{
    public const WEIGHT = 6;

    public const KEY_CUSTOM_VALUES = 'useCustomValues';

    public const DEFAULT_CUSTOM_VALUES = false;

    public const KEY_TRUE = 'true';

    public const DEFAULT_TRUE = '1';

    public const KEY_FALSE = 'false';

    public const DEFAULT_FALSE = '0';

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }

        $useCustomValues = $this->getConfig(static::KEY_CUSTOM_VALUES);
        $true = $useCustomValues ? $this->getConfig(static::KEY_TRUE) : static::DEFAULT_TRUE;
        $false = $useCustomValues ? $this->getConfig(static::KEY_FALSE) : static::DEFAULT_FALSE;
        if ($value instanceof BooleanValueInterface) {
            if ($useCustomValues) {
                if ($value->getValue()) {
                    return $false;
                }

                return $true;
            }

            return $value->negated();
        }

        if ($value === $true) {
            return $false;
        } elseif ($value === $false) {
            return $true;
        }

        return (bool)$value ? $false : $true;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $schema->addProperty(static::KEY_CUSTOM_VALUES, new BooleanSchema(static::DEFAULT_CUSTOM_VALUES));

        $trueSchema = new StringSchema(static::DEFAULT_TRUE);
        $trueSchema->getRenderingDefinition()->addVisibilityConditionByValue('../' . static::KEY_CUSTOM_VALUES)->addValue(true);
        $schema->addProperty(static::KEY_TRUE, $trueSchema);

        $falseSchema = new StringSchema(static::DEFAULT_FALSE);
        $falseSchema->getRenderingDefinition()->addVisibilityConditionByValue('../' . static::KEY_CUSTOM_VALUES)->addValue(true);
        $schema->addProperty(static::KEY_FALSE, $falseSchema);

        return $schema;
    }
}
