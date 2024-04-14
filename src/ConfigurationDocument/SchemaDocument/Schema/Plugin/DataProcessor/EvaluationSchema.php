<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\FieldContextSelectionSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class EvaluationSchema extends SwitchSchema
{
    public const TYPE = 'EVALUATION';

    public const TYPE_WITH_CONTEXT = 'EVALUATION_WITH_CONTEXT';

    public function __construct(bool $withContext = false, mixed $defaultValue = null)
    {
        if ($withContext) {
            $this->addProperty('inputContext', new CustomSchema(FieldContextSelectionSchema::TYPE_INPUT))->setWeight(-1);
        }

        parent::__construct('evaluation', $defaultValue);
    }
}
