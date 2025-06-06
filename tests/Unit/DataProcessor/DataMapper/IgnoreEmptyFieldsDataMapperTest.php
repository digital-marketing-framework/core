<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\IgnoreEmptyFieldsDataMapper;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class IgnoreEmptyFieldsDataMapperTest extends DataMapperTestBase
{
    protected const CLASS_NAME = IgnoreEmptyFieldsDataMapper::class;

    protected const KEYWORD = 'ignoreEmptyFields';

    protected const DEFAULT_CONFIG = [
        IgnoreEmptyFieldsDataMapper::KEY_ENABLED => IgnoreEmptyFieldsDataMapper::DEFAULT_ENABLED,
    ];

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
                [],
                [],
                [],
            ],
            [
                [],
                ['field1' => 'value1', 'field2' => 'value2'],
                [],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                [],
                ['field1' => '0', 'field2' => 'value2'],
                [],
                ['field1' => '0', 'field2' => 'value2'],
            ],
            [
                [],
                ['field2' => 'value2'],
                [],
                ['field1' => '', 'field2' => 'value2'],
            ],
            [
                [],
                [],
                [],
                ['field1' => '', 'field2' => ''],
            ],
            [
                [],
                ['field2' => new MultiValue(['']), 'field3' => new MultiValue(['value2'])],
                [],
                ['field1' => new MultiValue(), 'field2' => new MultiValue(['']), 'field3' => new MultiValue(['value2'])],
            ],
            [
                [],
                ['field1' => '', 'field2' => 'value2'],
                [IgnoreEmptyFieldsDataMapper::KEY_ENABLED => false],
                ['field1' => '', 'field2' => 'value2'],
            ],
            [
                [],
                ['field1' => new MultiValue(), 'field2' => new MultiValue(['']), 'field3' => new MultiValue(['value2'])],
                [IgnoreEmptyFieldsDataMapper::KEY_ENABLED => false],
                ['field1' => new MultiValue(), 'field2' => new MultiValue(['']), 'field3' => new MultiValue(['value2'])],
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
