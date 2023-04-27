<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class FirstOfValueSource extends ValueSource
{
    public const WEIGHT = 6;

    public function build(): null|string|ValueInterface
    {
        foreach ($this->configuration as $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());
            if ($value !== null) {
                return $value;
            }
        }
        return null;
    }

    public static function getSchema(): SchemaInterface
    {
        return new ListSchema(new CustomSchema(ValueSchema::TYPE));
    }
}
