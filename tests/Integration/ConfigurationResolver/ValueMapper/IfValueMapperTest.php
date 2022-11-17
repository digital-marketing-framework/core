<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\IfValueMapper;

/**
 * @covers IfValueMapper
 */
class IfValueMapperTest extends AbstractValueMapperTest
{
    /** @test */
    public function valueIfThenExists(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'value1' => [
                'if' => [
                    'field2' => 'value2',
                    'then' => 'value1b',
                    'else' => 'value1c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function valueIfThenDoesNotExist(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'value1' => [
                'if' => [
                    'field2' => 'value2',
                    'else' => 'value1c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function valueIfElseExists(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'value1' => [
                'if' => [
                    'field2' => 'value3',
                    'then' => 'value1b',
                    'else' => 'value1c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1c', $result);
    }

    /** @test */
    public function valueIfElseDoesNotExist(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'value1' => [
                'if' => [
                    'field2' => 'value3',
                    'then' => 'value1b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function constIfThenExists(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value2',
                'then' => 'value1b',
                'else' => 'value1c',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function constIfThenDoesNotExist(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value2',
                'else' => 'value1c',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function constIfElseExists(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value3',
                'then' => 'value1b',
                'else' => 'value1c',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1c', $result);
    }

    /** @test */
    public function constIfElseDoesNotExist(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value3',
                'then' => 'value1b',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function ifValueThen(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value2',
                'then' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
                'else' => [
                    'value1' => 'value1c',
                    'value2' => 'value2c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function ifValueElse(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value3',
                'then' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
                'else' => [
                    'value1' => 'value1c',
                    'value2' => 'value2c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1c', $result);
    }

    /** @test */
    public function discardedElsePartGetsComputedAsWell(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field3' => 'value3',
                'then' => ['content' => ['field' => 'field1']],
                'else' => ['content' => ['field' => 'field2']],
            ],
        ];

        $this->assertFalse($this->fieldTracker->hasBeenProcessed('field1'));
        $this->assertFalse($this->fieldTracker->hasBeenProcessed('field2'));

        $result = $this->runResolverProcess($config);
        
        $this->assertEquals('value1', $result);
        $this->assertTrue($this->fieldTracker->hasBeenProcessed('field1'));
        $this->assertTrue($this->fieldTracker->hasBeenProcessed('field2'));
    }

    /** @test */
    public function discardedThenPartGetsComputedAsWell(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field3' => 'value4',
                'then' => ['content' => ['field' => 'field1']],
                'else' => ['content' => ['field' => 'field2']],
            ],
        ];

        $this->assertFalse($this->fieldTracker->hasBeenProcessed('field1'));
        $this->assertFalse($this->fieldTracker->hasBeenProcessed('field2'));

        $result = $this->runResolverProcess($config);
        
        $this->assertEquals('value2', $result);
        $this->assertTrue($this->fieldTracker->hasBeenProcessed('field1'));
        $this->assertTrue($this->fieldTracker->hasBeenProcessed('field2'));
    }
}
