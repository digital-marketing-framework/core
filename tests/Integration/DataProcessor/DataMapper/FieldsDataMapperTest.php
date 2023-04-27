<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\FieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers FieldsDataMapper
 */
class FieldsDataMapperTest extends DataMapperTest
{
    protected const KEYWORD = 'fields';

    public function mapDataProvider(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                ['field1' => 'foo', 'field2' => '', 'field4' => new MultiValue(['bar'])],
                ['ext_field1' => 'foo', 'ext_field2' => '', 'ext_field4' => new MultiValue(['bar'])],
                [
                    FieldsDataMapper::KEY_FIELDS => [
                        'ext_field1' => $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field1'], 'field'),
                        'ext_field2' => $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field2'], 'field'),
                        'ext_field3' => $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'),
                        'ext_field4' => $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'),
                    ],
                ],
            ],
        ];
    }
}
