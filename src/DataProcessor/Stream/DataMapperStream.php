<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Stream;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class DataMapperStream extends Stream
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
        return 'Stream';
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
