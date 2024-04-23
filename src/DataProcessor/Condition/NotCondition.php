<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Condition;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ConditionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class NotCondition extends Condition
{
    public const WEIGHT = 3;

    public function evaluate(): bool
    {
        return !$this->dataProcessor->processCondition(
            $this->configuration,
            $this->context->copy()
        );
    }

    public static function getSchema(): SchemaInterface
    {
        return new CustomSchema(ConditionSchema::TYPE);
    }
}
