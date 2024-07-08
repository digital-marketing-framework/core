<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper
 */
class PassthroughFieldsDataMapperTest extends DataMapperTest
{
    protected const KEYWORD = 'passthroughFields';

    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker->markAsProcessed('field3');
    }

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
            [
                ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => true,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => '',
                ],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => true,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => 'field2',
                ],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'],
                ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => true,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => 'field3',
                ],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'],
                ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => true,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => 'field2,field3',
                ],
            ],
        ];
    }
}
