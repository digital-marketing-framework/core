<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Context;

use ArrayAccess;
use DigitalMarketingFramework\Core\ConfigurationResolver\FieldTrackerInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface ConfigurationResolverContextInterface extends ArrayAccess
{
    public function copy(): ConfigurationResolverContextInterface;

    public function getFieldTracker(): FieldTrackerInterface;
    public function getData(): DataInterface;
}
