<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper
 */
class PassthroughFieldsDataMapperTest extends DataMapperTest
{
    protected const KEYWORD = 'passthroughFields';

    public function mapDataProvider(): array
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
}
