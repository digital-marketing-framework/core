<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ValueSchema extends ContainerSchema
{
    public const TYPE = 'VALUE';

    public function __construct(
        RegistryInterface $registry,
        protected ?string $valueSourceKeyword = null,
        protected bool $constantValueSourceKeyword = false
    ) {
        parent::__construct();

        $valueModifierSchema = new CustomSchema(ValueModifierSchema::TYPE);
        if ($valueSourceKeyword === null) {
            $valueSourceSchema = new CustomSchema(ValueSourceSchema::TYPE);
        } else {
            $valueSourceSchema = new ValueSourceSchema($registry, $valueSourceKeyword, $constantValueSourceKeyword);
            if ($constantValueSourceKeyword) {
                $pluginClass = $registry->getPluginClass(ValueSourceInterface::class, $valueSourceKeyword);
                if ($pluginClass !== null && !$pluginClass::modifiable()) {
                    $valueModifierSchema = null;
                }
            }
        }

        $this->addProperty('data', $valueSourceSchema);

        if ($valueModifierSchema !== null) {
            $property = $this->addProperty('modifiers', $valueModifierSchema);
            $property->getRenderingDefinition()->setVisibilityConditionByValueSet('./data/type', ValueSourceSchema::VALUE_SET_VALUE_SOURCE_MODIFIABLE);
        }
    }

    public function getCustomType(): string
    {
        if ($this->valueSourceKeyword !== null) {
            if ($this->constantValueSourceKeyword) {
                return strtoupper($this->valueSourceKeyword) . '_' . static::TYPE;
            } else {
                return 'DEFAULT_' . strtoupper($this->valueSourceKeyword) . '_' . static::TYPE;
            }
        }
        return static::TYPE;
    }
}
