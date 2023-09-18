<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class DataProcessorPlugin extends ConfigurablePlugin implements DataProcessorPluginInterface, DataProcessorAwareInterface
{
    use DataProcessorAwareTrait;

    /**
     * @param array<string,mixed> $configuration
     */
    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
        protected array $configuration,
        protected DataProcessorContextInterface $context,
    ) {
        parent::__construct($keyword);
    }

    protected function fieldExists(string $key, bool $markAsProcessed = true): bool
    {
        if ($markAsProcessed) {
            $this->context->getFieldTracker()->markAsProcessed($key);
        }

        return $this->context->getData()->fieldExists($key);
    }

    protected function getFieldValue(string $key, bool $markAsProcessed = true): string|ValueInterface|null
    {
        return $this->fieldExists($key, $markAsProcessed)
            ? $this->context->getData()[$key]
            : null;
    }
}
