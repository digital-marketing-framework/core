<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\ExcludeFieldsDataMapper;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\DataMapper\ExcludeFieldsDataMapper
 */
class ExcludeFieldsDataMapperTest extends DataMapperTest
{
    protected const KEYWORD = 'excludeFields';

    protected function passthroughDataFirst(): bool
    {
        return true;
    }

    public function mapDataProvider(): array
    {
        return [
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => []],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field1', 'id1', 10),
                ]],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field1', 'id1', 10),
                    'id2' => $this->createListItem('field3', 'id2', 20),
                ]],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                [],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field1', 'id1', 10),
                    'id2' => $this->createListItem('field2', 'id2', 20),
                ]],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field3', 'id1', 10),
                ]],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field3', 'id1', 10),
                    'id2' => $this->createListItem('field4', 'id2', 20),
                ]],
            ],
        ];
    }
}
