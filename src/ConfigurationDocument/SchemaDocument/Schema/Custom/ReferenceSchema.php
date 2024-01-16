<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ScalarValues;

abstract class ReferenceSchema extends StringSchema
{
    public function __construct(
        ?string $defaultValue = null,
        bool $required = true,
        ?string $firstEmptyOptionLabel = null
    ) {
        parent::__construct($defaultValue);

        if ($required) {
            $this->setRequired();
        }

        if ($firstEmptyOptionLabel === null) {
            $firstEmptyOptionLabel = $this->getDefaultFirstEmptyOptionLabel();
        }

        $this->allowedValues->addValue('', $firstEmptyOptionLabel);

        $this->allowedValues->addReference(
            $this->getReferencePath(),
            label: $this->getLabel(),
            type: $this->getReferenceType()
        );
        $this->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
    }

    protected function getDefaultFirstEmptyOptionLabel(): string
    {
        return 'Please select';
    }

    abstract protected function getReferencePath(): string;

    abstract protected function getLabel(): string;

    protected function getReferenceType(): string
    {
        return ScalarValues::REFERENCE_TYPE_KEY;
    }
}
