<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;

class PassthroughFieldsDataMapperTest extends DataMapperTest
{
    protected const CLASS_NAME = PassthroughFieldsDataMapper::class;
    protected const KEYWORD = 'passthroughFields';

    protected const DEFAULT_CONFIG = [
        PassthroughFieldsDataMapper::KEY_ENABLED => PassthroughFieldsDataMapper::DEFAULT_ENABLED,
    ];

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
     * @test
     * @dataProvider mapDataDataProvider
     */
    public function mapDataTest(array $inputData, array $expectedOutputData, ?array $config = null, ?array $target = null): void
    {
        $this->mapData($inputData, $expectedOutputData, $config, $target);
    }
}
