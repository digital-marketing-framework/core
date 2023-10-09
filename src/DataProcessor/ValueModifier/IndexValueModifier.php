<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class IndexValueModifier extends ValueModifier
{
    public const WEIGHT = 30;

    public const KEY_INDEX = 'index';

    public const DEFAULT_INDEX = '';

    public function modify(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if (!$this->proceed()) {
            return $value;
        }

        $indexString = $this->getConfig(static::KEY_INDEX);
        $indices = $indexString !== '' ? explode(',', (string)$indexString) : [];

        $currentValue = $value;
        foreach ($indices as $index) {
            if (!$currentValue instanceof MultiValueInterface) {
                throw new DigitalMarketingFrameworkException(sprintf('Value is not a multi value and does not have an index "%s".', $index));
            }

            $currentValue = $currentValue[$index] ?? null;
        }

        return $currentValue;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_INDEX, new StringSchema(static::DEFAULT_INDEX));

        return $schema;
    }
}
