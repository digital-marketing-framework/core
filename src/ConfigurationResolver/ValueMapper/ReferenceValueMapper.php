<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class ReferenceValueMapper extends ValueMapper
{

    protected function resolveReference(string|array|null $valueMap): ?array
    {
        $processedReferences = [];
        /** @var ?ConfigurationInterface */
        $configuration = $this->context['configuration'] ?? null;
        while (!is_array($valueMap) && $valueMap !== null) {
            $loopFound = in_array($valueMap, $processedReferences);
            $processedReferences[] = $valueMap;
            if ($loopFound) {
                throw new DigitalMarketingFrameworkException(sprintf('Value map reference loop found: "%s"', implode('" > "', $processedReferences)));
            }
            $valueMap = $configuration?->getValueMapConfiguration((string)$valueMap);
        }
        return $valueMap;
    }

    public function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        $valueMap = $this->resolveReference($this->configuration);

        if ($valueMap !== null) {
            return $this->resolveValueMap($valueMap, $fieldValue);
        }

        return parent::resolveValue($fieldValue);
    }
}
