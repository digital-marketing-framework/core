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
                [ExcludeFieldsDataMapper::KEY_FIELDS => ''],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field1'],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field1,field3'],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                [],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field1,field2'],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field1' =>'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field3'],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field1' =>'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field3,field4'],
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
