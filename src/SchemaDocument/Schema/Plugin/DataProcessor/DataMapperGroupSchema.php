<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\Icon;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\FieldContextSelectionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

class DataMapperGroupSchema extends SwitchSchema
{
    public const TYPE = 'DATA_MAPPER_GROUP';

    public function __construct(mixed $defaultValue = null)
    {
        $this->addProperty('inputContext', new CustomSchema(FieldContextSelectionSchema::TYPE_INPUT))->setWeight(-2);
        $this->addProperty('outputContext', new CustomSchema(FieldContextSelectionSchema::TYPE_OUTPUT))->setWeight(-1);
        parent::__construct('dataMapperGroup', $defaultValue);
        $this->getRenderingDefinition()->setIcon(Icon::DATA_MAPPER_GROUP);
    }
}
