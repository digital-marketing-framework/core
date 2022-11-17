<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class DataMapPipelineContentResolver extends ContentResolver
{
    public function build(): string|ValueInterface|null
    {
        // TODO should we add an input key, like in DataMapContentResolver?
        $data = $this->context->getData();
        foreach ($this->configuration as $map) {
            $dataProcessor = $this->registry->getDataProcessor($map);
            $data = $dataProcessor->process($data);
        }
        return $data;
    }
}
