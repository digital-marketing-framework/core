<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\ExcludeFieldsDataMapper;

/**
 * @covers ExcludeFieldsDataMapper
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
                [ExcludeFieldsDataMapper::KEY_FIELDS => ''],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field1'],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field1,field3'],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                [],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field1,field2'],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field3'],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => 'field3,field4'],
            ],
        ];
    }
}
