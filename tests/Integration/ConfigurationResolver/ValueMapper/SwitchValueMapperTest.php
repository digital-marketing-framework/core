<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\SwitchValueMapper;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers SwitchValueMapper
 */
class SwitchValueMapperTest extends AbstractValueMapperTest
{
    /** @test */
    public function switchCaseMatches(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                1 => [
                    'case' => 'value1',
                    'value' => 'value1b'
                ],
                2 => [
                    'case' => 'value2',
                    'value' => 'value2b'
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function switchCaseDoesNotMatch(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                1 => [
                    'case' => 'value2',
                    'value' => 'value2b'
                ],
                2 => [
                    'case' => 'value3',
                    'value' => 'value3b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function switchSelfMatches(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                1 => [
                    ConfigurationResolverInterface::KEY_SELF => 'value1',
                    'value' => 'value1b'
                ],
                2 => [
                    'case' => 'value2',
                    'value' => 'value2b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function switchSelfDoesNotMatch(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                1 => [
                    ConfigurationResolverInterface::KEY_SELF => 'value2',
                    'value' => 'value2b'
                ],
                2 => [
                    'case' => 'value3',
                    'value' => 'value3b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function switchCaseMatchesKeyword(): void
    {
        $this->fieldValue = 'if';
        $config = [
            'switch' => [
                1 => [
                    'case' => 'if',
                    'value' => 'ifb',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('ifb', $result);
    }

    /** @test */
    public function switchUnsorted(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                2 => [
                    'case' => 'value1',
                    'value' => 'value1c',
                ],
                1 => [
                    'case' => 'value1',
                    'value' => 'value1b',
                ]
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function switchCaseIsNumberAndWillBeCastToString(): void
    {
        $this->fieldValue = '12';
        $config = [
            'switch' => [
                1 => [
                    'case' => 12,
                    'value' => 'twelve',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('twelve', $result);
    }
}
