<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class MultiValueValueSource extends ValueSource
{
    protected function getMultiValue(): MultiValueInterface
    {
        return new MultiValue();
    }

    public function build(): null|string|ValueInterface
    {
        $multiValue = $this->getMultiValue();
        foreach ($this->configuration as $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());
            if ($value !== null) {
                $multiValue[] = $value;
            }
        }
        return $multiValue;
    }

    public static function getSchema(): SchemaInterface
    {
        return new ListSchema(new CustomSchema(ValueSchema::TYPE));
    }
}
