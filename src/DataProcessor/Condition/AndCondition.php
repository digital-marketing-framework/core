<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Condition;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ConditionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class AndCondition extends Condition
{
    public const WEIGHT = 4;

    public const KEY_CONDITIONS = 'conditions';

    public function evaluate(): bool
    {
        $result = true;
        $subConditions = $this->getListConfig(static::KEY_CONDITIONS);
        foreach ($subConditions as $subConditionConfig) {
            if (!$this->dataProcessor->processCondition($subConditionConfig, $this->context->copy())) {
                $result = false;
            }
        }

        return $result;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_CONDITIONS, new ListSchema(new CustomSchema(ConditionSchema::TYPE)));

        return $schema;
    }
}
