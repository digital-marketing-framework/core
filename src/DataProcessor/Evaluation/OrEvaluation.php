<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\EvaluationSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class OrEvaluation extends Evaluation
{
    public const WEIGHT = 5;

    public const KEY_EVALUATIONS = 'evaluations';

    public function evaluate(): bool
    {
        $subEvaluations = $this->getListConfig(static::KEY_EVALUATIONS);
        if ($subEvaluations === []) {
            return true;
        }

        $result = false;
        foreach ($subEvaluations as $subEvaluationConfig) {
            if ($this->dataProcessor->processEvaluation($subEvaluationConfig, $this->context->copy())) {
                $result = true;
            }
        }

        return $result;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_EVALUATIONS, new ListSchema(new CustomSchema(EvaluationSchema::TYPE)));

        return $schema;
    }
}
