<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DateValueSource extends ValueSource
{
    public const KEY_VALUE = 'value';

    public const KEY_FORMAT = 'format';

    public const DEFAULT_FORMAT = 'Y-m-d';

    public function build(): string|ValueInterface|null
    {
        $value = $this->getConfig(static::KEY_VALUE);
        if ($value !== null) {
            $value = $this->dataProcessor->processValue($value, $this->context->copy());
        }

        if ($value === null) {
            return null;
        }

        $dateTimeValue = GeneralUtility::castValueToDateTimeValue($value, $this->getConfig(static::KEY_FORMAT));

        if ($dateTimeValue instanceof DateTimeValue) {
            return $dateTimeValue;
        }

        $this->logger->warning('Cannot convert value to date-time: "' . (string)$value . '"');

        return null;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_VALUE, new CustomSchema(ValueSchema::TYPE));
        $schema->addProperty(static::KEY_FORMAT, new StringSchema(static::DEFAULT_FORMAT));

        return $schema;
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}
