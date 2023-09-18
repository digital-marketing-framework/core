<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConcatenationValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;

class ConcatenationValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'concatenation';

    protected const CLASS_NAME = ConcatenationValueSource::class;

    protected const DEFAULT_CONFIG = [
        ConcatenationValueSource::KEY_GLUE => ConcatenationValueSource::DEFAULT_GLUE,
        ConcatenationValueSource::KEY_VALUES => ConcatenationValueSource::DEFAULT_VALUES,
    ];

    /** @test */
    public function emptyConfigurationLeadsToNullValue(): void
    {
        $output = $this->processValueSource([]);
        $this->assertNull($output);
    }

    /** @test */
    public function nonExistentFieldWillReturnNull(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
            ],
        ];
        $this->dataProcessor->method('processValue')->with($subConfig1)->willReturn(null);
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }

    /** @test */
    public function emptyFieldWillReturnEmptyValueAndNotNull(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
            ],
        ];
        $this->dataProcessor->method('processValue')->with($subConfig1)->willReturn('');
        $output = $this->processValueSource($config);
        $this->assertEquals('', $output);
    }

    /** @test */
    public function singleComlpexFieldWillBeReturnedAsIs(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
            ],
        ];
        $this->dataProcessor->method('processValue')->with($subConfig1)->willReturn(new MultiValue(['foo', 'bar']));
        /** @var MultiValueInterface $output */
        $output = $this->processValueSource($config);
        $this->assertInstanceOf(MultiValueInterface::class, $output);
        $this->assertEquals(['foo', 'bar'], $output->toArray());
    }

    /** @test */
    public function concatSimpleValues(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
            ],
        ];
        $this->dataProcessor->method('processValue')
            ->withConsecutive([$subConfig1], [$subConfig2])
            ->willReturnOnConsecutiveCalls('value1', 'value2');
        $output = $this->processValueSource($config);
        $this->assertEquals('value1 value2', $output);
    }

    /** @test */
    public function concatWithComplexValue(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
            ],
        ];
        $this->dataProcessor->method('processValue')
            ->withConsecutive([$subConfig1], [$subConfig2])
            ->willReturnOnConsecutiveCalls(new MultiValue(['value1.1', 'value1.2']), 'value2');
        $output = $this->processValueSource($config);
        $this->assertEquals('value1.1,value1.2 value2', $output);
    }

    /** @test */
    public function customGlueIsUsed(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
            ],
            ConcatenationValueSource::KEY_GLUE => '-',
        ];
        $this->dataProcessor->method('processValue')
            ->withConsecutive([$subConfig1], [$subConfig2])
            ->willReturnOnConsecutiveCalls('value1', 'value2');
        $output = $this->processValueSource($config);
        $this->assertEquals('value1-value2', $output);
    }
}
