<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Condition;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ConditionReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class ReferenceCondition extends Condition
{
    public const WEIGHT = 6;

    public const KEY_CONDITION_REFERENCE = 'conditionId';

    public function evaluate(): bool
    {
        $conditionId = $this->getConfig(static::KEY_CONDITION_REFERENCE);
        $conditionConfig = $this->context->getConfiguration()->getConditionConfiguration($conditionId);

        return $this->dataProcessor->processCondition(
            $conditionConfig,
            $this->context->copy()
        );
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema */
        $schema = parent::getSchema();

        $referenceSchema = new CustomSchema(ConditionReferenceSchema::TYPE);
        $schema->addProperty(static::KEY_CONDITION_REFERENCE, $referenceSchema);

        return $schema;
    }
}
