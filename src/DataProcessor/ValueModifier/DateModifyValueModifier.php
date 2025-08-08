<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DateModifyValueModifier extends ValueModifier
{
    public const KEY_MODIFIER = 'modify';

    public const DEFAULT_MODIFIER = '+1 day';

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        $modifier = $this->getConfig(static::KEY_MODIFIER);

        $dateTimeValue = GeneralUtility::castValueToDateTimeValue($value);

        if ($dateTimeValue instanceof DateTimeValue) {
            if ($dateTimeValue->getDate()->modify($modifier) === false) {
                $this->logger->warning('Date-time modifier cannot be applied: "' . $modifier . '"');
            }

            return $dateTimeValue;
        }

        $this->logger->warning('Cannot convert value to date-time: "' . (string)$value . '"');

        return $value;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_MODIFIER, new StringSchema(static::DEFAULT_MODIFIER));

        return $schema;
    }
}
