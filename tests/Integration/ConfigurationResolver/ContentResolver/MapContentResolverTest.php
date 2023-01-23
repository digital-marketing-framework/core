<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\MapContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

/**
 * @covers MapContentResolver
 */
class MapContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function map(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'values' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function noMap(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'values' => [
                    'value2' => 'value2b',
                    'value3' => 'value3b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function mapMultiValue(): void
    {
        $config = [
            'multiValue' => ['value1', 'value2', 'value3'],
            'map' => [
                'values' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                    'value3' => 'value3b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1b', 'value2b', 'value3b'], $result);
    }

    /** @test */
    public function noMapMultiValue(): void
    {
        $config = [
            'multiValue' => ['value1', 'value2', 'value3'],
            'map' => [
                'values' => [
                    'value4' => 'value4b',
                    'value5' => 'value5b',
                    'value6' => 'value6b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2', 'value3'], $result);
    }

    /** @test */
    public function someMapMultiValue(): void
    {
        $config = [
            'multiValue' => ['value1', 'value2', 'value3'],
            'map' => [
                'values' => [
                    'value1' => 'value1b',
                    'value3' => 'value3b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1b', 'value2', 'value3b'], $result);
    }

    /** @test */
    public function invertMap(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'invert' => true,
                'values' => [
                    'value1b' => 'value1',
                    'value2b' => 'value2',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function invertMapWithoutHit(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'invert' => true,
                'values' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function ignoreCase(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'vaLUe2',
            'map' => [
                'ignoreCase' => true,
                'values' => [
                    'value1' => 'value1b',
                    'ValuE2' => 'VALUE2b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('VALUE2b', $result);
    }

    /** @test */
    public function ignoreCaseAndInvert(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'vaLUe2',
            'map' => [
                'ignoreCase' => true,
                'invert' => true,
                'values' => [
                    'value1b' => 'value1',
                    'VALUE2b' => 'ValuE2',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('VALUE2b', $result);
    }

    /** @test */
    public function mapReferenceAsString(): void
    {
        $this->configurationResolverContext['configuration'] = [
            'valueMaps' => [
                'testReferenceMap' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
            ],
        ];
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => 'testReferenceMap',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function mapReferenceAsList(): void
    {
        $this->configurationResolverContext['configuration'] = [
            'valueMaps' => [
                'testReferenceMap' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
            ],
        ];
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'references' => [
                    'testReferenceMap',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function mapReferenceInverted(): void
    {
        $this->configurationResolverContext['configuration'] = [
            'valueMaps' => [
                'testReferenceMap' => [
                    'value1b' => 'value1',
                    'value2b' => 'value2',
                ],
            ],
        ];
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'invert' => true,
                'references' => [
                    'testReferenceMap',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function mapReferenceIgnoreCase(): void
    {
        $this->configurationResolverContext['configuration'] = [
            'valueMaps' => [
                'testReferenceMap' => [
                    'vALue1' => 'value1b',
                    'value2' => 'value2b',
                ],
            ],
        ];
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'ignoreCase' => true,
                'references' => [
                    'testReferenceMap',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function mapReferenceWithOverride(): void
    {
        $this->configurationResolverContext['configuration'] = [
            'valueMaps' => [
                'testReferenceMap' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
            ],
        ];
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'references' => [
                    'testReferenceMap',
                ],
                'values' => [
                    'value1' => 'value1c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1c', $result);
    }

    /** @test */
    public function mapReferenceWithReferenceOverride(): void
    {
        $this->configurationResolverContext['configuration'] = [
            'valueMaps' => [
                'testReferenceMap' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
                'testReferenceMap2' => [
                    'value1' => 'value1c',
                ],
            ],
        ];
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'references' => [
                    'testReferenceMap',
                    'testReferenceMap2',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1c', $result);
    }

    /** @test */
    public function mapReferenceWithNullOverride(): void
    {
        $this->configurationResolverContext['configuration'] = [
            'valueMaps' => [
                'testReferenceMap' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
            ],
        ];
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'references' => [
                    'testReferenceMap',
                ],
                'values' => [
                    'value1' => null,
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function directValueMapWithoutKeywordsIsRecognized(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'value1' => 'value1b',
                'value2' => 'value2b',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    public function keywordProvider(): array
    {
        return [
            ['references', []],
            ['ignoreCase', true],
            ['invert', true],
            ['values', []],
        ];
    }

    /**
     * @dataProvider keywordProvider
     * @test
     */
    public function directValueMapWitKeywordsWillThrowException(string $keyword, mixed $keywordValue): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'map' => [
                'value1' => 'value1b',
                $keyword => $keywordValue,
                'value2' => 'value2b',
            ],
        ];
        static::expectException(DigitalMarketingFrameworkException::class);
        static::expectExceptionMessage(sprintf('Found keyword "%s" in what is probably meant to be a value map.', $keyword));
        $this->runResolverProcess($config);
    }
}
