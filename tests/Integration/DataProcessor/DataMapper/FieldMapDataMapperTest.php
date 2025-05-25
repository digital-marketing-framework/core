<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\FieldMapDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FieldMapDataMapper::class)]
class FieldMapDataMapperTest extends DataMapperTestBase
{
    protected const KEYWORD = 'fieldMap';

    public static function mapDataProvider(): array
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
                    FieldMapDataMapper::KEY_FIELDS => [
                        'id1' => static::createMapItem('ext_field1', static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field1'], 'field'), 'id1', 10),
                        'id2' => static::createMapItem('ext_field2', static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field2'], 'field'), 'id2', 20),
                        'id3' => static::createMapItem('ext_field3', static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'), 'id3', 30),
                        'id4' => static::createMapItem('ext_field4', static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'), 'id4', 40),
                    ],
                ],
            ],
        ];
    }
}
