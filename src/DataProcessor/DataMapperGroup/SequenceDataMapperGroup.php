<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup;

use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\DataMapperGroup;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataMapperGroupReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class SequenceDataMapperGroup extends DataMapperGroup
{
    public const KEY_SEQUENCE_LIST = 'list';

    public const KEY_DATA_MAPPER_LOOP_DETECTION = 'dataMapperGroupIdsProcessed';

    public const MESSAGE_LOOP_DETECTED = 'Data mapper group reference loop found for ID %s!';

    public const MESSAGE_DATA_MAPPER_GROUP_NOT_FOUND = 'Data mapper group with ID %s not found!';

    protected function loopDetection(string $dataMapperGroupId, DataProcessorContextInterface $context): void
    {
        if (!isset($context[static::KEY_DATA_MAPPER_LOOP_DETECTION])) {
            $context[static::KEY_DATA_MAPPER_LOOP_DETECTION] = [];
        }

        if (isset($context[static::KEY_DATA_MAPPER_LOOP_DETECTION][$dataMapperGroupId])) {
            throw new DigitalMarketingFrameworkException(sprintf(static::MESSAGE_LOOP_DETECTED, $dataMapperGroupId));
        }
    }

    public function compute(): DataInterface
    {
        $subGroupIds = $this->getListConfig(static::KEY_SEQUENCE_LIST);
        $data = $subGroupIds === [] ? new Data() : $this->context->getData();
        foreach ($subGroupIds as $groupId) {
            $context = $this->context->copy(keepFieldTracker: false);
            $this->loopDetection($groupId, $context);
            $context[static::KEY_DATA_MAPPER_LOOP_DETECTION][$groupId] = true;

            $dataMapperGroupConfig = $this->context->getConfiguration()->getDataMapperGroupConfiguration($groupId);
            if ($dataMapperGroupConfig === null) {
                throw new DigitalMarketingFrameworkException(sprintf(static::MESSAGE_DATA_MAPPER_GROUP_NOT_FOUND, $groupId));
            }

            $data = $this->dataProcessor->processDataMapperGroup(
                $dataMapperGroupConfig,
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

        $schema->addProperty(static::KEY_SEQUENCE_LIST, new ListSchema(new CustomSchema(DataMapperGroupReferenceSchema::TYPE)));

        return $schema;
    }
}
