<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class PassthroughFieldsDataMapper extends WritingDataMapper
{
    protected function map(DataInterface $target)
    {
        foreach ($this->context->getData() as $fieldName => $value) {
            $this->addField($target, $fieldName, $value);
        }
    }
}
