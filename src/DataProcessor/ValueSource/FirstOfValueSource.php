<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class FirstOfValueSource extends ValueSource
{
    public const WEIGHT = 6;

    public const KEY_VALUE_LIST = 'listValues';

    public function build(): string|ValueInterface|null
    {
        $valueList = $this->getListConfig(static::KEY_VALUE_LIST);
        foreach ($valueList as $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_VALUE_LIST, new ListSchema(new CustomSchema(ValueSchema::TYPE)));

        return $schema;
    }
}
