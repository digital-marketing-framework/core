<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DateMalformedStringException;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DateModifyValueModifier extends ValueModifier implements GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    public const KEY_MODIFIER = 'modify';

    public const DEFAULT_MODIFIER = '+1 day';

    protected function getDefaultTimezone(): string
    {
        return $this->globalConfiguration->getGlobalSettings(CoreSettings::class)->getDefaultTimezone();
    }

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        $modifier = $this->getConfig(static::KEY_MODIFIER);
        $format = $value instanceof DateTimeValue ? $value->getFormat() : null;
        $timezone = $value instanceof DateTimeValue ? $value->getTimezone() : $this->getDefaultTimezone();

        $dateTimeValue = GeneralUtility::castValueToDateTimeValue($value, $format, $timezone);

        if ($dateTimeValue instanceof DateTimeValue) {
            try {
                // @phpstan-ignore identical.alwaysFalse (PHP 8.1/8.2 returns false, PHP 8.3+ throws exception)
                if ($dateTimeValue->getDate()->modify($modifier) === false) {
                    $this->logger->warning('Date-time modifier cannot be applied: "' . $modifier . '"');
                }
            } catch (DateMalformedStringException) {
                // PHP 8.3+ throws DateMalformedStringException for invalid modifiers
                $this->logger->warning('Date-time modifier cannot be applied: "' . $modifier . '"');
            }

            return $dateTimeValue;
        }

        $this->logger->warning('Cannot convert value to date-time: "' . $value . '"');

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
