<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class PassthroughFieldsDataMapper extends WritingDataMapper
{
    public const WEIGHT = 20;

    protected function map(DataInterface $target): void
    {
        foreach ($this->context->getData() as $fieldName => $value) {
            $this->addField($target, $fieldName, $value);
        }
    }
}
