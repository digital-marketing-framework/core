<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\FieldContextSelectionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

class ConditionSchema extends SwitchSchema
{
    public const TYPE = 'CONDITION';

    public const TYPE_WITH_CONTEXT = 'CONDITION_WITH_CONTEXT';

    public function __construct(bool $withContext = false, mixed $defaultValue = null)
    {
        if ($withContext) {
            $this->addProperty('inputContext', new CustomSchema(FieldContextSelectionSchema::TYPE_INPUT))->setWeight(-1);
        }

        parent::__construct('condition', $defaultValue);
    }
}
