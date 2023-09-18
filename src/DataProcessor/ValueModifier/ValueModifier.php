<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class ValueModifier extends DataProcessorPlugin implements ValueModifierInterface
{
    public const KEY_ENABLED = 'enabled';

    public const DEFAULT_ENABLED = false;

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        return $value;
    }

    protected function proceed(): bool
    {
        return $this->getConfig(static::KEY_ENABLED);
    }

    public function modify(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if (!$this->proceed()) {
            return $value;
        }

        if ($value instanceof MultiValueInterface) {
            $multiValueClass = $value::class;
            $modifiedValue = new $multiValueClass();
            foreach ($value as $index => $subValue) {
                $modifiedSubValue = $this->modify($subValue);
                if ($modifiedSubValue !== null) {
                    $modifiedValue[$index] = $modifiedSubValue;
                }
            }
        } else {
            $modifiedValue = $this->modifyValue($value);
        }

        return $modifiedValue;
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));

        return $schema;
    }
}
