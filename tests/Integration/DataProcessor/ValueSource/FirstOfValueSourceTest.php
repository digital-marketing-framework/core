<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FirstOfValueSource;

/**
 * @covers FirstOfValueSource
 */
class FirstOfValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'firstOf';

    /** @test */
    public function emptyConfigurationLeadsToNullValue(): void
    {
        $output = $this->processValueSource($this->getValueSourceConfiguration([]));
        $this->assertNull($output);
    }

    public function firstOfDataProvider(): array
    {
        return [
            [
                null,
                [
                    $this->getValueConfiguration([], 'null'),
                    $this->getValueConfiguration([], 'null'),
                    $this->getValueConfiguration([], 'null'),
                ],
            ],
            [
                'foo',
                [
                    $this->getValueConfiguration([], 'null'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'foo'], 'constant'),
                    $this->getValueConfiguration([], 'null'),
                ],
            ],
            [
                '',
                [
                    $this->getValueConfiguration([], 'null'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => ''], 'constant'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'),
                ],
            ],
            [
                'a',
                [
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'b'], 'constant'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'c'], 'constant'),
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider firstOfDataProvider
     */
    public function firstOf(mixed $expectedResult, array $config): void
    {
        
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertEquals($expectedResult, $output);
    }
}
