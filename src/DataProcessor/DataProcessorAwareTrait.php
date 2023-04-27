<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

trait DataProcessorAwareTrait
{
    protected DataProcessorInterface $dataProcessor;

    public function setDataProcessor(DataProcessorInterface $dataProcessor): void
    {
        $this->dataProcessor = $dataProcessor;
    }
}
