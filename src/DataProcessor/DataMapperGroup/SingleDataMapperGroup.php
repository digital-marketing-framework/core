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
        // The field tracker is scoped to a single data mapper group.
        // Within the group, the field map marks the fields it consumed so that the
        // passthrough and field collector mechanisms can pick up the remaining ones.
        // Beyond the group those marks are meaningless: a subsequent group in a sequence
        // operates on the output of its predecessor, so the tracked names refer to a
        // different set of fields than the ones the next group is about to process.
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
