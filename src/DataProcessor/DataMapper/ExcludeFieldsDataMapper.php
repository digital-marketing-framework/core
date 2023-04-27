<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class ExcludeFieldsDataMapper extends DataMapper
{
    public const KEY_FIELDS = 'fields';
    public const DEFAULT_FIELDS = '';

    protected function map(DataInterface $target)
    {
        $excludeFields = GeneralUtility::castValueToArray($this->getConfig(static::KEY_FIELDS));
        foreach ($excludeFields as $excludeField) {
            if ($target->fieldExists($excludeField)) {
                unset($target[$excludeField]);
            }
        }
    }

    public static function getDefaultConfiguration(?bool $enabled = null): array
    {
        return parent::getDefaultConfiguration($enabled) + [
            static::KEY_FIELDS => static::DEFAULT_FIELDS,
        ];
    }
}
