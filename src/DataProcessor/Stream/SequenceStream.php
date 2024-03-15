<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Stream;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\StreamReferenceSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class SequenceStream extends Stream
{
    public const KEY_SEQUENCE_LIST = 'list';

    public const KEY_STREAM_LOOP_DETECTION = 'streamIdsProcessed';

    public const MESSAGE_LOOP_DETECTED = 'Stream reference loop found for ID %s!';

    public const MESSAGE_STREAM_NOT_FOUND = 'Stream with ID %s not found!';

    protected function loopDetection(string $streamId, DataProcessorContextInterface $context): void
    {
        if (!isset($context[static::KEY_STREAM_LOOP_DETECTION])) {
            $context[static::KEY_STREAM_LOOP_DETECTION] = [];
        }

        if (isset($context[static::KEY_STREAM_LOOP_DETECTION][$streamId])) {
            throw new DigitalMarketingFrameworkException(sprintf(static::MESSAGE_LOOP_DETECTED, $streamId));
        }
    }

    public function compute(): DataInterface
    {
        $subStreamIds = $this->getListConfig(static::KEY_SEQUENCE_LIST);
        $data = $subStreamIds === [] ? new Data() : $this->context->getData();
        foreach ($subStreamIds as $streamId) {
            $context = $this->context->copy(keepFieldTracker: false);
            $this->loopDetection($streamId, $context);
            $context[static::KEY_STREAM_LOOP_DETECTION][$streamId] = true;

            $streamConfig = $this->context->getConfiguration()->getStreamConfiguration($streamId);
            if ($streamConfig === null) {
                throw new DigitalMarketingFrameworkException(sprintf(static::MESSAGE_STREAM_NOT_FOUND, $streamId));
            }

            $data = $this->dataProcessor->processStream(
                $streamConfig,
                $context
            );
        }

        return $data;
    }

    public static function getLabel(): ?string
    {
        return 'Sequence';
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema */
        $schema = parent::getSchema();

        $schema->addProperty(static::KEY_SEQUENCE_LIST, new ListSchema(new CustomSchema(StreamReferenceSchema::TYPE)));

        return $schema;
    }
}
