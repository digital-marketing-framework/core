<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\IgnoreEmptyFieldsDataMapper;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class IgnoreEmptyFieldsDataMapperTest extends DataMapperTest
{
    protected const CLASS_NAME = IgnoreEmptyFieldsDataMapper::class;
    protected const KEYWORD = 'ignoreEmptyFields';

    public function mapDataDataProvider(): array
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
