<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class SingleDataMapperGroup extends DataMapperGroup
{
    public const KEY_DATA_MAPPER = 'data';

    public function compute(): DataInterface
    {
        return $this->dataProcessor->processDataMapper(
            $this->getConfig(static::KEY_DATA_MAPPER),
            $this->context->copy(keepFieldTracker: false)
        );
    }

    public static function getLabel(): ?string
    {
        return 'Field Mapping';
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema */
        $schema = parent::getSchema();

        $dataMapperSchema = new CustomSchema(DataMapperSchema::TYPE);
        $dataMapperSchema->getRenderingDefinition()->setSkipHeader(true);
        $schema->addProperty(static::KEY_DATA_MAPPER, $dataMapperSchema);

        return $schema;
    }
}
