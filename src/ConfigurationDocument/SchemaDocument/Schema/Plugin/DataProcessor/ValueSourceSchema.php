<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\PluginSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ValueSourceSchema extends PluginSchema
{
    public const TYPE = 'VALUE_SOURCE';

    public const VALUE_SET_VALUE_SOURCE_MODIFIABLE = 'valueSource/modifiable';
    public const VALUE_SET_VALUE_SOURCE_KEYWORDS = 'valueSource/all';
    public const VALUE_SET_VALUE_SOURCE_CAN_BE_MULTI_VALUE = 'valueSource/canBeMultiValue';

    protected StringSchema $typeSchema;
    protected ContainerSchema $configSchema;

    public function __construct(
        RegistryInterface $registry,
        protected ?string $keyword = null,
        protected bool $constant = false
    ) {
        parent::__construct(
            $registry,
            $constant ? $keyword : null,
        );
    }

    protected function init(): void
    {
        $this->typeSchema = new StringSchema();
        if (!$this->constant) {
            $this->typeSchema->getRenderingDefinition()->setFormat('select');
            $this->typeSchema->getAllowedValues()->addValueSet(static::VALUE_SET_VALUE_SOURCE_KEYWORDS);
            if ($this->keyword !== null) {
                $this->typeSchema->setDefaultValue($this->keyword);
            }
        } else {
            $this->typeSchema->setValue($this->keyword);
            $this->typeSchema->getRenderingDefinition()->setFormat('hidden');
            $this->typeSchema->getAllowedValues()->addValue($this->keyword);
        }
        $this->configSchema = new ContainerSchema();
        $this->addProperty('type', $this->typeSchema);
        $this->addProperty('config', $this->configSchema);
    }

    public function addValueSource(string $keyword, SchemaInterface $schema): void
    {
        $this->valueSets[static::VALUE_SET_VALUE_SOURCE_KEYWORDS][] = $keyword;
        $property = $this->configSchema->addProperty($keyword, $schema);
        $property->getRenderingDefinition()->setVisibilityConditionByString('../type', $keyword);
    }

    public function processPlugin(string $keyword, string $class): void
    {
        $this->addValueSource($keyword, $class::getSchema());
        if ($class::modifiable()) {
            $this->valueSets[static::VALUE_SET_VALUE_SOURCE_MODIFIABLE][] = $keyword;
        }
        if ($class::canBeMultiValue()) {
            $this->valueSets[static::VALUE_SET_VALUE_SOURCE_CAN_BE_MULTI_VALUE][] = $keyword;
        }
    }

    protected function getPluginInterface(): string
    {
        return ValueSourceInterface::class;
    }
}
