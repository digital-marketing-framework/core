<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class TestCaseSchema extends ContainerSchema
{
    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);

        $this->addProperty('label', new StringSchema(''));
        $this->addProperty('name', new StringSchema(''));

        $descriptionSchema = new StringSchema('');
        $descriptionSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_TEXT);
        $this->addProperty('description', $descriptionSchema);

        $typeSchema = new StringSchema('');
        // TODO define value set testCaseType/all
        // $typeSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        // $typeSchema->getAllowedValues()->addValueSet('testCaseType/all');
        $this->addProperty('type', $typeSchema);

        $this->addProperty('hash', new StringSchema(''));

        $inputSchema = new StringSchema('');
        $inputSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_TEXT);
        $this->addProperty('serialized_input', $inputSchema);

        $expectedOutputSchema = new StringSchema('');
        $expectedOutputSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_TEXT);
        $this->addProperty('serialized_expected_output', $expectedOutputSchema);
    }
}
