<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DefaultValueModifier extends ValueModifier
{
    public const WEIGHT = 100;

    public const KEY_VALUE = 'value';

    public const DEFAULT_VALUE = '';

    public function modify(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if (!$this->proceed()) {
            return $value;
        }

        if (GeneralUtility::isEmpty($value)) {
            return $this->getConfig(static::KEY_VALUE);
        }

        return $value;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_VALUE, new StringSchema(static::DEFAULT_VALUE));

        return $schema;
    }
}
