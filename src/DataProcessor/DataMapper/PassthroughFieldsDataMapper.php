<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

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
            $this->addField($target, (string)$fieldName, $value);
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
