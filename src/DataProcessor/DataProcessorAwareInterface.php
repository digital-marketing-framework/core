<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

interface DataProcessorAwareInterface
{
    public function setDataProcessor(DataProcessorInterface $dataProcessor): void;
}
