<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class ListValueSource extends ValueSource
{
    public const WEIGHT = 5;

    public const KEY_VALUES = 'values';

    protected function getMultiValue(): MultiValueInterface
    {
        return new MultiValue();
    }

    public function build(): string|ValueInterface|null
    {
        $multiValue = $this->getMultiValue();
        $values = $this->getListConfig(static::KEY_VALUES);
        foreach ($values as $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());
            if ($value !== null) {
                $multiValue[] = $value;
            }
        }

        return $multiValue;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_VALUES, new ListSchema(new CustomSchema(ValueSchema::TYPE)));

        return $schema;
    }
}
