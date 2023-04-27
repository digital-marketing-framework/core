<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;

class PassthroughFieldsDataMapperTest extends DataMapperTest
{
    protected const CLASS_NAME = PassthroughFieldsDataMapper::class;
    protected const KEYWORD = 'passthroughFields';

    public function mapDataDataProvider(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
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
