<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class NegateValueModifier extends ValueModifier
{
    public const WEIGHT = 6;

    public const KEY_TRUE = 'true';
    public const DEFAULT_TRUE = null;
    public const FALLBACK_TRUE = '1';

    public const KEY_FALSE = 'false';
    public const DEFAULT_FALSE = null;
    public const FALLBACK_FALSE = '0';

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }

        $true = $this->getConfig(static::KEY_TRUE);
        $false = $this->getConfig(static::KEY_FALSE);
        if ($value instanceof BooleanValueInterface) {
            if ((bool)$value->getValue() && $false !== null) {
                return $false;
            } elseif (!$value->getValue() && $true !== null) {
                return $true;
            }
            return $value->negated();
        } else {
            if ($true === null) {
                $true = static::FALLBACK_TRUE;
            }
            if ($false === null) {
                $false = static::FALLBACK_FALSE;
            }
            if ($value === $true) {
                return $false;
            } elseif ($value === $false) {
                return $true;
            }
            return (bool)$value ? $false : $true;
        }
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_TRUE, new StringSchema(static::FALLBACK_TRUE));
        $schema->addProperty(static::KEY_FALSE, new StringSchema(static::FALLBACK_FALSE));
        return $schema;
    }
}
