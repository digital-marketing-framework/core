<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class MultiValueValueSource extends ValueSource
{
    public const KEY_VALUES = 'values';

    protected function getMultiValue(): MultiValueInterface
    {
        return new MultiValue();
    }

    public function build(): string|ValueInterface|null
    {
        $multiValue = $this->getMultiValue();
        $values = $this->getMapConfig(static::KEY_VALUES);
        foreach ($values as $key => $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());
            if ($value !== null) {
                $multiValue[$key] = $value;
            }
        }

        return $multiValue;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_VALUES, new MapSchema(new CustomSchema(ValueSchema::TYPE)));

        return $schema;
    }
}
