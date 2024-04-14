<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class FieldValueSource extends ValueSource
{
    public const WEIGHT = 2;

    public const KEY_FIELD_NAME = 'fieldName';

    public const DEFAULT_FIELD_NAME = '';

    public function build(): string|ValueInterface|null
    {
        $fieldName = $this->getConfig(static::KEY_FIELD_NAME);
        if ($fieldName === '') {
            throw new DigitalMarketingFrameworkException('Field value source: field name not provided.');
        }

        return $this->getFieldValue($fieldName);
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $fieldName = new StringSchema();
        $fieldName->getRenderingDefinition()->setLabel('Source Field Name');
        $fieldName->getRenderingDefinition()->addRole(RenderingDefinitionInterface::ROLE_INPUT_FIELD);
        $fieldName->getSuggestedValues()->setContextual();
        $schema->addProperty(static::KEY_FIELD_NAME, $fieldName);

        return $schema;
    }
}
