<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DateFormatValueModifier extends ValueModifier implements GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    public const KEY_FORMAT = 'format';

    public const DEFAULT_FORMAT = 'Y-m-d';

    protected function getDefaultTimezone(): string
    {
        return $this->globalConfiguration->getGlobalSettings(CoreSettings::class)->getDefaultTimezone();
    }

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        $format = $this->getConfig(static::KEY_FORMAT);
        $timezone = $value instanceof DateTimeValue ? $value->getTimezone() : $this->getDefaultTimezone();

        $dateTimeValue = GeneralUtility::castValueToDateTimeValue($value, $format, $timezone);

        if ($dateTimeValue instanceof DateTimeValue) {
            return $dateTimeValue;
        }

        $this->logger->warning('Cannot convert value to date-time: "' . $value . '"');

        return $value;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_FORMAT, new StringSchema(static::DEFAULT_FORMAT));

        return $schema;
    }
}
