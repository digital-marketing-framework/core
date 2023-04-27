<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\FieldsDataMapper;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class FieldsDataMapperTest extends DataMapperTest
{
    protected const CLASS_NAME = FieldsDataMapper::class;
    protected const KEYWORD = 'fields';

    /** @test */
    public function noFields(): void
    {
        $config = [
            DataMapper::KEY_ENABLED => true,
            FieldsDataMapper::KEY_FIELDS => [],
        ];
        $output = $this->processDataMapper($config);
        $this->assertMultiValueEmpty($output);
    }

    /** @test */
    public function fields(): void
    {
        $this->dataProcessor->method('processValue')->withConsecutive(
            [['k1' => 'v1']],
            [['k2' => 'v2']],
            [['k3' => 'v3']],
            [['k4' => 'v4']],
        )->willReturnOnConsecutiveCalls(
            'foo',
            '',
            null,
            new MultiValue(['bar']),
        );
        $config = [
            DataMapper::KEY_ENABLED => true,
            FieldsDataMapper::KEY_FIELDS => [
                'ext_field1' => ['k1' => 'v1'],
                'ext_field2' => ['k2' => 'v2'],
                'ext_field3' => ['k3' => 'v3'],
                'ext_field4' => ['k4' => 'v4'],
            ],
        ];
        $output = $this->processDataMapper($config);
        $this->assertMultiValueEquals(['ext_field1' => 'foo', 'ext_field2' => '', 'ext_field4' => ['bar']], $output);
    }
}
