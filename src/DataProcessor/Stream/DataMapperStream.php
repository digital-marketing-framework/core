<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Stream;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class DataMapperStream extends Stream
{
    public function compute(): DataInterface
    {
        return $this->dataProcessor->processDataMapper(
            $this->configuration,
            $this->context->copy(keepFieldTracker: false)
        );
    }

    public static function getLabel(): ?string
    {
        return 'Stream';
    }

    public static function getSchema(): SchemaInterface
    {
        return new CustomSchema(DataMapperSchema::TYPE);
    }
}
