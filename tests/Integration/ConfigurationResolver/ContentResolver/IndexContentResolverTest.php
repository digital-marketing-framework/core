<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IndexContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers IndexContentResolver
 */
class IndexContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function fieldDoesNotExist(): void
    {
        $config = [
            'field' => 'field1',
            'index' => '0',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }
    
    /** @test */
    public function fieldIsNoMultiValue(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => 'field1',
            'index' => '0',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function indexDoesNotExist(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field1',
            'index' => '2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function fieldAndIndexExists(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field1',
            'index' => '1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1.2', $result);
    }

    /** @test */
    public function fieldIndexIsEmpty(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', '']);
        $config = [
            'field' => 'field1',
            'index' => '1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNotNull($result);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function fieldIndexHasMultiValue(): void
    {
        $this->data['field1'] = new MultiValue(['value1', new MultiValue(['value2.1', 'value2.2'])]);
        $config = [
            'field' => 'field1',
            'index' => '1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value2.1','value2.2'], $result);
    }

    /** @test */
    public function fieldIndexHasEmptyMultiValue(): void
    {
        $this->data['field1'] = new MultiValue(['value1', new MultiValue()]);
        $config = [
            'field' => 'field1',
            'index' => '1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEmpty($result);
    }

    /** @test */
    public function nestedFieldIndexAsString(): void
    {
        $this->data['field1'] = new MultiValue([
            'value0',
            new MultiValue([
                'value1.0',
                new MultiValue([
                    'value1.1.0',
                    'value1.1.1',
                ]),
            ]),
        ]);
        $config = [
            'field' => 'field1',
            'index' => '1,1,0'
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1.1.0', $result);
    }

    /** @test */
    public function nestedFieldIndexAsList(): void
    {
        $this->data['field1'] = new MultiValue([
            'value0',
            new MultiValue([
                'value1.0',
                new MultiValue([
                    'value1.1.0',
                    'value1.1.1',
                ]),
            ]),
        ]);
        $config = [
            'field' => 'field1',
            'index' => [
                'list' => [1, 1, 0],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1.1.0', $result);
    }

    /** @test */
    public function nestedIndexDoesNotExist(): void
    {
        $this->data['field1'] = new MultiValue([
            'value0',
            new MultiValue([
                'value1.0',
                new MultiValue([
                    'value1.1.0',
                    'value1.1.1',
                ]),
            ]),
        ]);
        $config = [
            'field' => 'field1',
            'index' => '0,2,0',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function nestedIndexIsNotAMultiValue(): void
    {
        $this->data['field1'] = new MultiValue([
            'value0',
            new MultiValue([
                'value1.0',
                'value1.1',
            ]),
        ]);
        $config = [
            'field' => 'field1',
            'index' => '1,1,0',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function multiValueDoesNotComeFromField(): void
    {
        $config = [
            'multiValue' => ['value0', 'value1'],
            'index' => '1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function nestedMultiValueDoesNotComeFromField(): void
    {
        $config = [
            'multiValue' => [
                'value0', 
                [
                    'multiValue' => [
                        'value1.0', 
                        'value1.1'
                    ],
                ]
            ],
            'index' => '1,1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1.1', $result);
    }

    /** @test */
    public function indexHasToBeProcessed(): void
    {
        $this->data['field1'] = new MultiValue(['value0', 'value1', 'value2']);
        $this->data['field2'] = 'value2';
        $config = [
            'field' => 'field1',
            'index' => [
                'if' => [
                    'field2' => 'value2',
                    'then' => '2',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function indexHasToBeProcessedButResolvesToNullResultsInIndexBeingIgnored(): void
    {
        $this->data['field1'] = new MultiValue(['value0', 'value1', 'value2']);
        $this->data['field2'] = 'value2';
        $config = [
            'field' => 'field1',
            'index' => [
                'if' => [
                    'field2' => 'value3',
                    'then' => '2',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value0', 'value1', 'value2'], $result);
    }

    /** @test */
    public function fieldAndIndexHaveToBeProcessedRecursively(): void
    {
        $this->data['field1'] = new MultiValue(['value0', 'value1', 'value2']);
        $this->data['field2'] = new MultiValue(['field1', '2']);
        $config = [
            'field' => [
                'field' => 'field2',
                'index' => '0',
            ],
            'index' => [
                'field' => 'field2',
                'index' => '1',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function emptyIndicesAsStringResultsInOriginalMultiValue(): void
    {
        $this->data['field1'] = new MultiValue(['value0', 'value1']);
        $config = [
            'field' => 'field1',
            'index' => '',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value0', 'value1'], $result);
    }

    /** @test */
    public function emptyIndicesAsListResultsInOriginalMultiValue(): void
    {
        $this->data['field1'] = new MultiValue(['value0', 'value1']);
        $config = [
            'field' => 'field1',
            'index' => ['list' => []],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value0', 'value1'], $result);
    }

    /** @test */
    public function emptyIndicesAsStringResultsInOriginalScalarValue(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => 'field1',
            'index' => '',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function emptyIndicesAsListResultsInOriginalScalarValue(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => 'field1',
            'index' => ['list' => []],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }
}
