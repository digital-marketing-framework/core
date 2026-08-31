<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class VariableValueSource extends ValueSource
{
    public const WEIGHT = 3;

    public const KEY_VARIABLE_NAME = 'reference';

    public const DEFAULT_VARIABLE_NAME = '';

    /**
     * A declared but empty variable yields an empty string, an undeclared one yields null.
     * The distinction matters downstream: FirstOfValueSource falls through on null only,
     * and a null value may drop the field from the mapped output entirely.
     */
    public function build(): ?string
    {
        return $this->context->getConfiguration()->getVariableConfiguration(
            $this->getStringConfig(static::KEY_VARIABLE_NAME)
        );
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $variableNameSchema = new StringSchema(static::DEFAULT_VARIABLE_NAME);
        $variableNameSchema->setRequired();
        $variableNameSchema->getRenderingDefinition()->setLabel('Variable');
        $variableNameSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $variableNameSchema->getAllowedValues()->addValue(static::DEFAULT_VARIABLE_NAME, 'Please select');
        $variableNameSchema->getAllowedValues()->addReference('/dataProcessing/variables/*', label: '{key}');
        $schema->addProperty(static::KEY_VARIABLE_NAME, $variableNameSchema);

        return $schema;
    }
}
