<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\EvaluationSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class NotEvaluation extends Evaluation
{
    public const WEIGHT = 3;

    public function evaluate(): bool
    {
        return !$this->dataProcessor->processEvaluation(
            $this->configuration,
            $this->context->copy()
        );
    }

    public static function getSchema(): SchemaInterface
    {
        return new CustomSchema(EvaluationSchema::TYPE);
    }
}
