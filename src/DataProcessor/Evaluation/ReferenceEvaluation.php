<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\EvaluationReferenceSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class ReferenceEvaluation extends Evaluation
{
    public const WEIGHT = 6;

    public const KEY_EVALUATION_REFERENCE = 'evaluationId';

    public function evaluate(): bool
    {
        $evaluationId = $this->getConfig(static::KEY_EVALUATION_REFERENCE);
        $evaluationConfig = $this->context->getConfiguration()->getEvaluationConfiguration($evaluationId);

        return $this->dataProcessor->processEvaluation(
            $evaluationConfig,
            $this->context->copy()
        );
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema */
        $schema = parent::getSchema();

        $referenceSchema = new CustomSchema(EvaluationReferenceSchema::TYPE);
        $schema->addProperty(static::KEY_EVALUATION_REFERENCE, $referenceSchema);

        return $schema;
    }
}
