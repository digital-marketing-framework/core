<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class DataMapContentResolver extends ContentResolver
{
    protected const KEY_INPUT = 'input';
    protected const KEY_MAP = 'map';

    protected function resolveDataMap($dataMap): ?array
    {
        $processedReferences = [];
        /** @var ?ConfigurationInterface */
        $collectorConfiguration = $this->context['configuration'] ?? null;
        while (!is_array($dataMap) && $dataMap !== null) {
            $loopFound = in_array($dataMap, $processedReferences);
            $processedReferences[] = $dataMap;
            if ($loopFound) {
                throw new DigitalMarketingFrameworkException(sprintf('Data map reference loop found: %s', implode('>', $processedReferences)));
            }
            $dataMap = $collectorConfiguration?->getDataMapConfiguration((string)$dataMap);
        }
        return $dataMap;
    }

    public function build(): string|ValueInterface|null
    {
        // find input data source
        if ($this->configuration[static::KEY_INPUT] ?? false) {
            $input = $this->resolveContent(['dataMap' => $this->getConfig(static::KEY_INPUT)], $this->context->copy(keepFieldTracker:false));
            $dataMap = $this->getConfig(static::KEY_MAP);
        } else {
            $input = $this->context->getData();
            $dataMap = $this->configuration[static::KEY_MAP] ?? $this->configuration;
        }

        // find data map
        $dataMap = $this->resolveDataMap($dataMap);

        // process input and map
        if ($input instanceof DataInterface) {
            if ($dataMap !== null) {
                $dataProcessor = $this->registry->getDataProcessor($dataMap);
                return $dataProcessor->process($input, $this->context->toArray());
            }
            return $input;
        }

        return null;
    }
}
