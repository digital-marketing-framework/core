<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class WritingDataMapper extends DataMapper
{
    public const KEY_OVERWRITE = 'overwrite';
    public const DEFAULT_OVERWRITE = false;

    protected function addField(DataInterface $data, string $fieldName, string|null|ValueInterface $value): void
    {
        if ($value !== null && ($this->getConfig(static::KEY_OVERWRITE) || $data->fieldEmpty($fieldName))) {
            $data[$fieldName] = $value;
        }
    }

    public static function getDefaultConfiguration(?bool $enabled = null): array
    {
        return parent::getDefaultConfiguration($enabled) + [
            static::KEY_OVERWRITE => static::DEFAULT_OVERWRITE,
        ];
    }
}
