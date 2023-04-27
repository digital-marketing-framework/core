<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class ComparisonEvaluation extends Evaluation
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
