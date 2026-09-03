<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapperGroup;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\FieldMapDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\SequenceDataMapperGroup;
use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\SingleDataMapperGroup;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConcatenationValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldCollectorValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataProcessorPluginTestBase;

abstract class DataMapperGroupTestBase extends DataProcessorPluginTestBase
{
    /** @var array<string,string|ValueInterface|null> */
    protected array $data = [];

    /** @var array<int,array<string,mixed>> */
    protected array $configuration = [[]];

    /**
     * Data mapper groups that can be referenced by ID, as a sequence would reference them.
     *
     * @var array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    protected array $dataMapperGroups = [];

    /**
     * Registers a data mapper group under an ID so that a sequence can reference it.
     *
     * @param array<string,mixed> $groupConfig
     */
    protected function registerDataMapperGroup(string $id, array $groupConfig, int $weight = 10): void
    {
        $this->dataMapperGroups[$id] = static::createMapItem($id, $groupConfig, $id, $weight);
    }

    /**
     * @param array<string,mixed> $groupConfig
     */
    protected function processDataMapperGroup(array $groupConfig): DataInterface
    {
        $configuration = $this->configuration;
        $configuration[0][ConfigurationInterface::KEY_DATA_PROCESSING][ConfigurationInterface::KEY_DATA_MAPPER_GROUPS] = $this->dataMapperGroups;

        $dataProcessor = $this->registry->getDataProcessor();

        // The context is created the way the real entry points create it, without a field tracker
        // of our own. Injecting one would hide the very thing these tests are about: which part of
        // the processing chain provides the tracker and where a new one begins.
        $context = $dataProcessor->createContext(new Data($this->data), new Configuration($configuration));

        return $dataProcessor->processDataMapperGroup($groupConfig, $context);
    }

    // -- group configuration builders --

    /**
     * @param array<string,mixed> $dataMapperConfig
     *
     * @return array<string,mixed>
     */
    protected static function singleGroup(array $dataMapperConfig): array
    {
        return [
            'type' => 'single',
            'config' => [
                'single' => [
                    SingleDataMapperGroup::KEY_DATA_MAPPER => $dataMapperConfig,
                ],
            ],
        ];
    }

    /**
     * @param array<string> $groupIds
     *
     * @return array<string,mixed>
     */
    protected static function sequenceGroup(array $groupIds): array
    {
        $list = [];
        $weight = 10;
        foreach ($groupIds as $groupId) {
            $list['seq.' . $groupId] = static::createListItem($groupId, 'seq.' . $groupId, $weight);
            $weight += 10;
        }

        return [
            'type' => 'sequence',
            'config' => [
                'sequence' => [
                    SequenceDataMapperGroup::KEY_SEQUENCE_LIST => $list,
                ],
            ],
        ];
    }

    // -- data mapper configuration builders --

    /**
     * Field map in target field order. Values are Value configurations, as built by
     * fieldValue(), concatenatedFields() or collectedFields().
     *
     * @param array<string,array<string,mixed>> $fields
     *
     * @return array<string,mixed>
     */
    protected static function fieldMap(array $fields): array
    {
        $map = [];
        $weight = 10;
        foreach ($fields as $targetFieldName => $valueConfig) {
            $id = 'map.' . $targetFieldName;
            $map[$id] = static::createMapItem($targetFieldName, $valueConfig, $id, $weight);
            $weight += 10;
        }

        return [
            'fieldMap' => [
                FieldMapDataMapper::KEY_FIELDS => $map,
            ],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    protected static function passthrough(bool $unprocessedOnly = true, string $includeFields = ''): array
    {
        return [
            'passthroughFields' => [
                PassthroughFieldsDataMapper::KEY_ENABLED => true,
                PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => $unprocessedOnly,
                PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => $includeFields,
            ],
        ];
    }

    // -- value configuration builders --

    /**
     * @return array<string,mixed>
     */
    protected static function fieldValue(string $fieldName): array
    {
        return static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => $fieldName], 'field');
    }

    /**
     * Reads several fields and folds them into a single value, as "name" would be built
     * from "firstName" and "lastName".
     *
     * @return array<string,mixed>
     */
    protected static function concatenatedFields(string ...$fieldNames): array
    {
        $values = [];
        $weight = 10;
        foreach ($fieldNames as $fieldName) {
            $id = 'concat.' . $fieldName;
            $values[$id] = static::createListItem(static::fieldValue($fieldName), $id, $weight);
            $weight += 10;
        }

        return static::getValueConfiguration([
            ConcatenationValueSource::KEY_GLUE => '\\s',
            ConcatenationValueSource::KEY_VALUES => $values,
        ], 'concatenation');
    }

    /**
     * @return array<string,mixed>
     */
    protected static function collectedFields(bool $unprocessedOnly = true, string $include = '', string $exclude = ''): array
    {
        return static::getValueConfiguration([
            FieldCollectorValueSource::KEY_UNPROCESSED_ONLY => $unprocessedOnly,
            FieldCollectorValueSource::KEY_IGNORE_IF_EMPTY => false,
            FieldCollectorValueSource::KEY_INCLUDE => $include,
            FieldCollectorValueSource::KEY_EXCLUDE => $exclude,
            FieldCollectorValueSource::KEY_TEMPLATE => '{key}',
        ], 'fieldCollector');
    }
}
