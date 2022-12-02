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

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function getFieldTracker(): FieldTrackerInterface
    {
        return $this->fieldTracker;
    }

    public function getData(): DataInterface
    {
        return $this->data;
    }

    public function copy(bool $keepFieldTracker = true, ?DataInterface $data = null): ConfigurationResolverContextInterface
    {
        if ($data === null) {
            $data = $this->data;
        }
        if ($keepFieldTracker) {
            return new ConfigurationResolverContext($data, $this->toArray(), $this->fieldTracker);
        } else {
            return new ConfigurationResolverContext($data, $this->toArray());
        }
    }
}
