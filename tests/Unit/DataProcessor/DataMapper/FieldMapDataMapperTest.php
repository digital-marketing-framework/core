<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\FieldMapDataMapper;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Tests\TestUtilityTrait;
use PHPUnit\Framework\Attributes\Test;

class FieldMapDataMapperTest extends DataMapperTestBase
{
    use TestUtilityTrait;

    protected const CLASS_NAME = FieldMapDataMapper::class;

    protected const KEYWORD = 'fields';

    #[Test]
    public function noFields(): void
    {
        $config = [
            FieldMapDataMapper::KEY_FIELDS => [],
        ];
        $output = $this->processDataMapper($config);
        $this->assertMultiValueEmpty($output);
    }

    #[Test]
    public function fields(): void
    {
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [['k1' => 'v1']],
            [['k2' => 'v2']],
            [['k3' => 'v3']],
            [['k4' => 'v4']],
        ], [
            'foo',
            '',
            null,
            new MultiValue(['bar']),
        ]);
        $config = [
            FieldMapDataMapper::KEY_FIELDS => [
                'id1' => $this->createMapItem('ext_field1', ['k1' => 'v1'], 'id1', 10),
                'id2' => $this->createMapItem('ext_field2', ['k2' => 'v2'], 'id2', 20),
                'id3' => $this->createMapItem('ext_field3', ['k3' => 'v3'], 'id3', 30),
                'id4' => $this->createMapItem('ext_field4', ['k4' => 'v4'], 'id4', 40),
            ],
        ];
        $output = $this->processDataMapper($config);
        $this->assertMultiValueEquals(['ext_field1' => 'foo', 'ext_field2' => '', 'ext_field4' => ['bar']], $output);
    }
}
