<?php

namespace DigitalMarketingFramework\Core\Model\DataSet;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Context\ContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface DataSetInterface
{
    public function getData(): DataInterface;
    public function getConfiguration(): ConfigurationInterface;
    public function getContext(): ContextInterface;
}
