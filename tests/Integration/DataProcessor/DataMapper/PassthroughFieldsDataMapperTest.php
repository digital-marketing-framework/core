<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;

/**
 * @covers PassthroughFieldsDataMapper
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
