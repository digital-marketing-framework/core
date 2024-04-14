<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;

class FieldContextSelectionSchema extends StringSchema
{
    public const TYPE_INPUT = 'INPUT_CONTEXT_SELECTION';

    public const TYPE_OUTPUT = 'OUTPUT_CONTEXT_SELECTION';

    public function __construct(bool $input = true)
    {
        parent::__construct();
        $this->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $this->getAllowedValues()->addValue('', 'None');
        if ($input) {
            $this->getAllowedValues()->addInputFieldContextSelection();
        } else {
            $this->getAllowedValues()->addOutputFieldContextSelection();
        }
    }
}
