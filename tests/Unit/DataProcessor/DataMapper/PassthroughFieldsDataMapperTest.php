<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class PassthroughFieldsDataMapperTest extends DataMapperTest
{
    protected const CLASS_NAME = PassthroughFieldsDataMapper::class;

    protected const KEYWORD = 'passthroughFields';

    protected const DEFAULT_CONFIG = [
        PassthroughFieldsDataMapper::KEY_ENABLED => PassthroughFieldsDataMapper::DEFAULT_ENABLED,
    ];

    /**
     * @return array<array{
     *  0:array<string,string|ValueInterface|null>,
     *  1:array<string,string|ValueInterface|null>,
     *  2?:?array<string,mixed>,
     *  3?:array<string,string|ValueInterface|null>
     * }>
     */
    public function mapDataDataProvider(): array
    {
        return [
            [
                [],
                [],
                [PassthroughFieldsDataMapper::KEY_ENABLED => true],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [PassthroughFieldsDataMapper::KEY_ENABLED => true],
            ],
            [
                [],
                [],
                [PassthroughFieldsDataMapper::KEY_ENABLED => false],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                [],
                [PassthroughFieldsDataMapper::KEY_ENABLED => false],
            ],
        ];
    }

    /**
     * @param array<string,string|ValueInterface|null> $inputData
     * @param array<string,string|ValueInterface|null> $expectedOutputData
     * @param ?array<string,mixed> $config
     * @param array<string,string|ValueInterface|null> $target
     *
     * @test
     *
     * @dataProvider mapDataDataProvider
     */
    public function mapDataTest(array $inputData, array $expectedOutputData, ?array $config = null, ?array $target = null): void
    {
        $this->mapData($inputData, $expectedOutputData, $config, $target);
    }
}
