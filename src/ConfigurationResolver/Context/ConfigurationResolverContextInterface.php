<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Context;

use ArrayAccess;
use DigitalMarketingFramework\Core\ConfigurationResolver\FieldTrackerInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface ConfigurationResolverContextInterface extends ArrayAccess
{
    public function copy(bool $keepFieldTracker = true, ?DataInterface $data = null): ConfigurationResolverContextInterface;
    public function toArray(): array;

    public function getFieldTracker(): FieldTrackerInterface;
    public function getData(): DataInterface;
}
