<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Context;

use ArrayObject;
use DigitalMarketingFramework\Core\ConfigurationResolver\FieldTracker;
use DigitalMarketingFramework\Core\ConfigurationResolver\FieldTrackerInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class ConfigurationResolverContext extends ArrayObject implements ConfigurationResolverContextInterface
{
    public function __construct(
        protected DataInterface $data,
        array $context = [], 
        protected FieldTrackerInterface $fieldTracker = new FieldTracker())
    {
        $context['data'] = $data;
        $context['tracker'] = $this->fieldTracker;
        parent::__construct($context);
    }

    public function getFieldTracker(): FieldTrackerInterface
    {
        return $this->fieldTracker;
    }

    public function getData(): DataInterface
    {
        return $this->data;
    }

    public function copy(): ConfigurationResolverContextInterface
    {
        return new ConfigurationResolverContext($this->data, iterator_to_array($this), $this->fieldTracker);
    }
}
