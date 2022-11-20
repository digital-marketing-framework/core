<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IfContentResolver;

/**
 * @covers IfContentResolver
 */
class IfContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
    }

    public function ifProvider(): array
    {
        return [
            // evalTrue, then, else, expected
            [true,  null,         null,         null],
            [false, null,         null,         null],

            [true,  'value-then', null,         'value-then'],
            [false, 'value-then', null,         null],

            [true,  null,          'value-else', null],
            [false, null,          'value-else', 'value-else'],

            [true,  'value-then', 'value-else', 'value-then'],
            [false, 'value-then', 'value-else', 'value-else'],
        ];
    }

    protected function runIfThenElse(bool $evalTrue, mixed $then, mixed $else, mixed $expected, bool $useNullOnThen, bool $useNullOnElse): void
    {
        $config = [
            'if' => [
                'field1' => $evalTrue ? 'value1' : 'value2',
            ],
        ];
        if ($useNullOnThen || $then !== null) {
            $config['if']['then'] = $then;
        }
        if ($useNullOnElse || $else !== null) {
            $config['if']['else'] = $else;
        }
        $result = $this->runResolverProcess($config);
        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @dataProvider ifProvider
     * @test
     */
    public function ifThenElse(bool $evalTrue, mixed $then, mixed $else, mixed $expected): void
    {
        $this->runIfThenElse($evalTrue, $then, $else, $expected, false, false);
        if ($then === null) {
            $this->runIfThenElse($evalTrue, $then, $else, $expected, true, false);
        }
        if ($else === null) {
            $this->runIfThenElse($evalTrue, $then, $else, $expected, false, true);
        }
        if ($then === null && $else === null) {
            $this->runIfThenElse($evalTrue, $then, $else, $expected, true, true);
        }
    }

    /** @test */
    public function nestedIf(): void
    {
        $config = [
            'if' => [
                'field1' => 'value1',
                'then' => [
                    'if' => [
                        'field2' => 'value1',
                        'else' => 'expected-value',
                    ],
                ],
            ]
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('expected-value', $result);
    }
}
