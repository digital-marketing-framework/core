<?php

namespace DigitalMarketingFramework\Core\Service;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface DataProcessorInterface
{
    public const KEY_FIELDS = 'fields';
    public const DEFAULT_FIELDS = [];

    public const KEY_PASSTHROUGH_FIELDS = 'passthroughFields';
    public const DEFAULT_PASSTHROUGH_FIELDS = false;

    public const KEY_IGNORE_EMPTY_FIELDS = 'ignoreEmptyFields';
    public const DEFAULT_IGNORE_EMPTY_FIELDS = false;

    public const KEY_EXCLUDE_FIELDS = 'excludeFields';
    public const DEFAULT_EXCLUDE_FIELDS = [];

    public function process(DataInterface $data, array $context = []): DataInterface;
}
