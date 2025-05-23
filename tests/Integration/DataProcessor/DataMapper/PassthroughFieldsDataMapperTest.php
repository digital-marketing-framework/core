<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PassthroughFieldsDataMapper::class)]
class PassthroughFieldsDataMapperTest extends DataMapperTestBase
{
    protected const KEYWORD = 'passthroughFields';

    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker->markAsProcessed('field3');
    }

    public static function mapDataProvider(): array
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
