<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\DataMapper\IgnoreEmptyFieldsDataMapper
 */
class IgnoreEmptyFieldsDataMapperTest extends DataMapperTest
{
    protected const KEYWORD = 'ignoreEmptyFields';

    protected function passthroughDataFirst(): bool
    {
        return true;
    }

    public function mapDataProvider(): array
    {
        return [
            [
                [],
                [],
                [],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                ['field1' => '0', 'field2' => 'value2'],
                ['field1' => '0', 'field2' => 'value2'],
            ],
            [
                ['field1' => '', 'field2' => 'value2'],
                ['field2' => 'value2'],
            ],
            [
                ['field1' => '', 'field2' => ''],
                [],
            ],
            [
                ['field1' => new MultiValue(), 'field2' => new MultiValue(['']), 'field3' => new MultiValue(['value2'])],
                ['field2' => new MultiValue(['']), 'field3' => new MultiValue(['value2'])],
            ],
        ];
    }
}
