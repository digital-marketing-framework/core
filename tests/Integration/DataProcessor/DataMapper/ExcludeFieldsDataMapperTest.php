<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\ExcludeFieldsDataMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExcludeFieldsDataMapper::class)]
class ExcludeFieldsDataMapperTest extends DataMapperTestBase
{
    protected const KEYWORD = 'excludeFields';

    protected function passthroughDataFirst(): bool
    {
        return true;
    }

    public static function mapDataProvider(): array
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
                    'id1' => static::createListItem('field1', 'id1', 10),
                ]],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => static::createListItem('field1', 'id1', 10),
                    'id2' => static::createListItem('field3', 'id2', 20),
                ]],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                [],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => static::createListItem('field1', 'id1', 10),
                    'id2' => static::createListItem('field2', 'id2', 20),
                ]],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => static::createListItem('field3', 'id1', 10),
                ]],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => static::createListItem('field3', 'id1', 10),
                    'id2' => static::createListItem('field4', 'id2', 20),
                ]],
            ],
        ];
    }
}
