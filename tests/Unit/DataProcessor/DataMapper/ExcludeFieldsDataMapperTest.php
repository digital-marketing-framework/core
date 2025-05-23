<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\ExcludeFieldsDataMapper;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ExcludeFieldsDataMapperTest extends DataMapperTestBase
{
    protected const CLASS_NAME = ExcludeFieldsDataMapper::class;

    protected const KEYWORD = 'excludeFields';

    /**
     * @return array<array{
     *  0:array<string,string|ValueInterface|null>,
     *  1:array<string,string|ValueInterface|null>,
     *  2?:?array<string,mixed>,
     *  3?:array<string,string|ValueInterface|null>
     * }>
     */
    public static function mapDataDataProvider(): array
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
                    static::createListItem('field1', 'id1', 10),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => static::createListItem('field1', 'id1', 10),
                    'id2' => static::createListItem('field3', 'id2', 20),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                [],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => static::createListItem('field1', 'id1', 10),
                    'id2' => static::createListItem('field2', 'id2', 20),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => static::createListItem('field3', 'id1', 10),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field1' => 'value1', 'field2' => 'value2'],
                [ExcludeFieldsDataMapper::KEY_FIELDS => [
                    'id1' => static::createListItem('field3', 'id1', 10),
                    'id2' => static::createListItem('field4', 'id2', 20),
                ]],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
        ];
    }

    /**
     * @param array<string,string|ValueInterface|null> $inputData
     * @param array<string,string|ValueInterface|null> $expectedOutputData
     * @param ?array<string,mixed> $config
     * @param array<string,string|ValueInterface|null> $target
     */
    #[Test]
    #[DataProvider('mapDataDataProvider')]
    public function mapDataTest(array $inputData, array $expectedOutputData, ?array $config = null, ?array $target = null): void
    {
        $this->mapData($inputData, $expectedOutputData, $config, $target);
    }
}
