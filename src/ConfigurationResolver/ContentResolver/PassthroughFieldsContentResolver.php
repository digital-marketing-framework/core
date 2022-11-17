<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class PassthroughFieldsContentResolver extends AbstractFieldCollectorContentResolver
{
    protected const DEFAULT_IGNORE_IF_EMPTY = false;
    protected const DEFAULT_UNPROCESSED_ONLY = false;

    protected function getMultiValue(): MultiValueInterface
    {
        return new Data();
    }

    protected function processField(string|int $key, string|ValueInterface|null $value): string|ValueInterface|null
    {
        return $value;
    }
}
