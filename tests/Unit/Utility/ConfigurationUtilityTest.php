<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Utility;

use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;
use PHPUnit\Framework\TestCase;

class ConfigurationUtilityTest extends TestCase
{
    /** @test */
    public function mergeEmpty(): void
    {
        $result = ConfigurationUtility::mergeConfiguration([], []);
        $this->assertEquals([], $result);
    }

    /**
     * @return array<array{0:array<string,mixed>,1:array<string,mixed>,2:array<string,mixed>}>
     */
    public function mergeDontResolveNullProvider(): array
    {
        return [
            // target, source, expected
            'All empty' => [[], [], []],

            'Target key does not exist' => [
                [],
                ['key1' => 'value1'],
                ['key1' => 'value1'],
            ],

            'Target key does not exist and source value is null' => [
                [],
                ['key1' => null],
                ['key1' => null],
            ],

            'Target value is null and source value is array' => [
                ['key1' => null],
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => ['key1.1' => 'value1.1']],
            ],

            'Target value is scalar and source value is array' => [
                ['key1' => 'value1'],
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => ['key1.1' => 'value1.1']],
            ],

            'Target value is array and source value is null' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => null],
                ['key1' => null],
            ],

            'Target value is array and source value scalar' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => 'value1'],
                ['key1' => 'value1'],
            ],

            'Target value is scalar and source value is null' => [
                ['key1' => 'value1'],
                ['key1' => null],
                ['key1' => null],
            ],

            'Target value is null and source value is null' => [
                ['key1' => null],
                ['key1' => null],
                ['key1' => null],
            ],

            'Target value is scalar and source value is scalar' => [
                ['key1' => 'value1'],
                ['key1' => 'value1b'],
                ['key1' => 'value1b'],
            ],

            'Target value is array and source value is array' => [
                ['key1' => ['key1.1' => 'value1.1', 'key1.2' => 'value1.2']],
                ['key1' => ['key1.1' => 'value1.1b', 'key1.3' => 'value1.3b']],
                ['key1' => ['key1.1' => 'value1.1b', 'key1.2' => 'value1.2', 'key1.3' => 'value1.3b']],
            ],

            'Nested arrays' => [
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1b']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1b']]],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $target
     * @param array<string,mixed> $source
     * @param array<string,mixed> $expected
     *
     * @dataProvider mergeDontResolveNullProvider
     *
     * @test
     */
    public function mergeDontResolveNull(array $target, array $source, array $expected): void
    {
        $result = ConfigurationUtility::mergeConfiguration($target, $source, false);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:array<string,mixed>,1:array<string,mixed>,2:array<string,mixed>}>
     */
    public function mergeResolveNullProvider(): array
    {
        return [
            // target, source, expected
            'All empty' => [[], [], []],

            'Target key does not exist' => [
                [],
                ['key1' => 'value1'],
                ['key1' => 'value1'],
            ],

            'Target key does not exist and source value is null' => [
                [],
                ['key1' => null],
                [],
            ],

            'Target value is null and source value is array' => [
                ['key1' => null],
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => ['key1.1' => 'value1.1']],
            ],

            'Target value is scalar and source value is array' => [
                ['key1' => 'value1'],
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => ['key1.1' => 'value1.1']],
            ],

            'Target value is array and source value is null' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => null],
                [],
            ],

            'Target value is array and source value scalar' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => 'value1'],
                ['key1' => 'value1'],
            ],

            'Target value is scalar and source value is null' => [
                ['key1' => 'value1'],
                ['key1' => null],
                [],
            ],

            'Target value is null and source value is null' => [
                ['key1' => null],
                ['key1' => null],
                [],
            ],

            'Target value is scalar and source value is scalar' => [
                ['key1' => 'value1'],
                ['key1' => 'value1b'],
                ['key1' => 'value1b'],
            ],

            'Target value is array and source value is array' => [
                ['key1' => ['key1.1' => 'value1.1', 'key1.2' => 'value1.2']],
                ['key1' => ['key1.1' => 'value1.1b', 'key1.3' => 'value1.3b']],
                ['key1' => ['key1.1' => 'value1.1b', 'key1.2' => 'value1.2', 'key1.3' => 'value1.3b']],
            ],

            'Nested arrays' => [
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1b']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1b']]],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $target
     * @param array<string,mixed> $source
     * @param array<string,mixed> $expected
     *
     * @dataProvider mergeResolveNullProvider
     *
     * @test
     */
    public function mergeResolveNull(array $target, array $source, array $expected): void
    {
        $result = ConfigurationUtility::mergeConfiguration($target, $source, true);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param array<string,mixed> $target
     * @param array<string,mixed> $source
     * @param array<string,mixed> $expected
     *
     * @dataProvider mergeDontResolveNullProvider
     * @dataProvider mergeResolveNullProvider
     *
     * @test
     */
    public function resolvedNullDirectlyEqualsUnresolvedNullThenResolvedNull(array $target, array $source, array $expected): void
    {
        $unresolved = ConfigurationUtility::mergeConfiguration($target, $source, false);
        $expected = ConfigurationUtility::resolveNullInMergedConfiguration($unresolved);

        $result = ConfigurationUtility::mergeConfiguration($target, $source, true);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:array<string,mixed>,1:array<string,mixed>}>
     */
    public function resolveNullProvider(): array
    {
        return [
            // config, expected
            'Empty' => [[], []],

            'Scalar value' => [
                ['key1' => 'value1'],
                ['key1' => 'value1'],
            ],

            'Null' => [
                ['key1' => null],
                [],
            ],

            'Array' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => ['key1.1' => 'value1.1']],
            ],

            'Array with null' => [
                ['key1' => ['key1.1' => null]],
                ['key1' => []],
            ],

            'Nested array with null' => [
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1', 'key1.1.2' => null, 'key1.1.3' => 'value1.1.3']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1', 'key1.1.3' => 'value1.1.3']]],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $config
     * @param array<string,mixed> $expected
     *
     * @dataProvider resolveNullProvider
     *
     * @test
     */
    public function resolveNull(array $config, array $expected): void
    {
        $result = ConfigurationUtility::resolveNullInMergedConfiguration($config);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:array<string,mixed>,1:array<string,mixed>,2:array<string,mixed>}>
     */
    public function splitConfigurationProvider(): array
    {
        return [
            'allEmpty' => [[], [], []],
            'parentEmpty' => [
                [],
                ['a' => 'A'],
                ['a' => 'A'],
            ],
            'mergedEmpty' => [
                ['a' => 'A'],
                [],
                ['a' => null],
            ],
            'scalarValues' => [
                ['a' => 'A', 'b' => 'B', 'c' => 'C'],
                ['b' => 'B2', 'c' => 'C', 'd' => 'D'],
                ['a' => null, 'b' => 'B2', 'd' => 'D'],
            ],
            'subValues' => [
                [
                    'a' => ['a.a' => 'A.A', 'a.b' => 'A.B'],
                    'b' => ['b.a' => 'B.A'],
                    'd' => ['d.a' => 'D.A'],
                ],
                [
                    'a' => ['a.a' => 'A.A2', 'a.b' => 'A.B'],
                    'b' => ['b.a' => 'B.A'],
                    'c' => ['c.a' => 'C.A'],
                ],
                [
                    'a' => ['a.a' => 'A.A2'],
                    'c' => ['c.a' => 'C.A'],
                    'd' => null,
                ],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $parentConfiguration
     * @param array<string,mixed> $mergedConfiguration
     * @param array<string,mixed> $expectedSplitConfiguration
     *
     * @dataProvider splitConfigurationProvider
     *
     * @test
     */
    public function splitConfiguration(array $parentConfiguration, array $mergedConfiguration, array $expectedSplitConfiguration): void
    {
        $splitConfiguration = ConfigurationUtility::splitConfiguration($parentConfiguration, $mergedConfiguration);
        $this->assertEquals($expectedSplitConfiguration, $splitConfiguration);
    }

    /** @test */
    public function splitConfigurationWithInconsistentStructures(): void
    {
        $parentConfiguration = [
            'a' => ['a.a' => 'A.A'],
        ];
        $mergedConfiguration = [
            'a' => 'A',
        ];
        $this->expectExceptionMessage('config:split found inconsistent structure for key "a"');
        ConfigurationUtility::splitConfiguration($parentConfiguration, $mergedConfiguration);
    }
}
