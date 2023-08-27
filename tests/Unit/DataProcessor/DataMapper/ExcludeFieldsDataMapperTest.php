<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\ExcludeFieldsDataMapper;

class ExcludeFieldsDataMapperTest extends DataMapperTest
{
    protected const CLASS_NAME = ExcludeFieldsDataMapper::class;
    protected const KEYWORD = 'excludeFields';

    public function mapDataDataProvider(): array
    {
        return [
            [
                [],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => []],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    $this->createListItem('field1', 'id1', 10),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field1', 'id1', 10),
                    'id2' => $this->createListItem('field3', 'id2', 20),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                [],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field1', 'id1', 10),
                    'id2' => $this->createListItem('field2', 'id2', 20),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field1' =>'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field3', 'id1', 10),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field1' =>'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => $this->createListItem('field3', 'id1', 10),
                    'id2' => $this->createListItem('field4', 'id2', 20),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider mapDataDataProvider
     */
    public function mapDataTest(array $inputData, array $expectedOutputData, ?array $config = null, ?array $target = null): void
    {
        $this->mapData($inputData, $expectedOutputData, $config, $target);
    }
}
