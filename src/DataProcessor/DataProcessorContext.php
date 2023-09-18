<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

use ArrayObject;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

/**
 * @extends ArrayObject<string,mixed>
 */
class DataProcessorContext extends ArrayObject implements DataProcessorContextInterface
{
    public function __construct(
        protected DataInterface $data,
        protected ConfigurationInterface $configuration,
        protected FieldTrackerInterface $fieldTracker = new FieldTracker()
    ) {
        parent::__construct([
            'data' => $data,
            'configuration' => $configuration,
            'tracker' => $fieldTracker,
        ]);
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

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    public function copy(bool $keepFieldTracker = true, ?DataInterface $data = null, ?ConfigurationInterface $configuration = null): DataProcessorContextInterface
    {
        if (!$data instanceof DataInterface) {
            $data = $this->data;
        }

        if (!$configuration instanceof ConfigurationInterface) {
            $configuration = $this->configuration;
        }

        if ($keepFieldTracker) {
            $copy = new DataProcessorContext($data, $configuration, $this->fieldTracker);
        } else {
            $copy = new DataProcessorContext($data, $configuration);
        }

        foreach ($this as $key => $value) {
            if (!in_array($key, ['data', 'configuration', 'tracker'])) {
                $copy[$key] = $value;
            }
        }

        return $copy;
    }
}
