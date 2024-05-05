<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Condition;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class ComparisonCondition extends Condition
{
    public const WEIGHT = 2;

    public function evaluate(): bool
    {
        return $this->dataProcessor->processComparison($this->configuration, $this->context->copy());
    }

    public static function getSchema(): SchemaInterface
    {
        return new CustomSchema(ComparisonSchema::TYPE);
    }
}
