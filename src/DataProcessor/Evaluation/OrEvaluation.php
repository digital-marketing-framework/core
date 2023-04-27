<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\EvaluationSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class OrEvaluation extends Evaluation
{
    public const WEIGHT = 5;

    public function evaluate(): bool
    {
        if (empty($this->configuration)) {
            return true;
        }
        $result = false;
        foreach ($this->configuration as $subEvaluationConfig) {
            if ($this->dataProcessor->processEvaluation($subEvaluationConfig, $this->context->copy())) {
                $result = true;
            }
        }
        return $result;
    }

    public static function getSchema(): SchemaInterface
    {
        return new ListSchema(new CustomSchema(EvaluationSchema::TYPE));
    }
}
