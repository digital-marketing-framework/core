<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class PassthroughFieldsDataMapper extends DataMapper
{
    public const WEIGHT = 20;

    public const KEY_ENABLED = 'enabled';

    public const DEFAULT_ENABLED = false;

    public function mapData(DataInterface $target): DataInterface
    {
        if (!$this->getConfig(static::KEY_ENABLED)) {
            return $target;
        }

        foreach ($this->context->getData() as $fieldName => $value) {
            $this->addField($target, $fieldName, $value);
        }

        return $target;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setSkipHeader(true);

        $enabledSchema = new BooleanSchema(static::DEFAULT_ENABLED);
        $enabledSchema->getRenderingDefinition()->setLabel('Passthrough Fields');
        $schema->addProperty(static::KEY_ENABLED, $enabledSchema);

        return $schema;
    }
}
