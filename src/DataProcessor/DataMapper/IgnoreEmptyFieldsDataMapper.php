<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IgnoreEmptyFieldsDataMapper extends DataMapper
{
    protected function map(DataInterface $target)
    {
        $toDeleteList = [];
        foreach ($target as $fieldName => $value) {
            if (GeneralUtility::isEmpty($value)) {
                $toDeleteList[] = $fieldName;
            }
        }
        foreach ($toDeleteList as $toDelete) {
            unset($target[$toDelete]);
        }
    }
}