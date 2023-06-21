<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class DataProcessorPlugin extends ConfigurablePlugin implements DataProcessorPluginInterface, DataProcessorAwareInterface
{
    use DataProcessorAwareTrait;

    // protected const KEY_WEIGHT = 'weight';

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected array $configuration,
        protected DataProcessorContextInterface $context,
    ) {
        parent::__construct($keyword, $registry);
    }

    protected function fieldExists($key, bool $markAsProcessed = true): bool
    {
        if ($markAsProcessed) {
            $this->context->getFieldTracker()->markAsProcessed($key);
        }
        return $this->context->getData()->fieldExists($key);
    }

    protected function getFieldValue(string $key, bool $markAsProcessed = true): string|ValueInterface|null
    {
        $fieldValue = $this->fieldExists($key, $markAsProcessed)
            ? $this->context->getData()[$key]
            : null;
        return $fieldValue;
    }

    // public function getWeight(): int
    // {
    //     return $this->getConfig(static::KEY_WEIGHT);
    // }
}
