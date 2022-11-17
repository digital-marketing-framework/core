<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\FirstOfContentResolver;

/**
 * @covers FirstOfContentResolver
 */
class FirstOfContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function multipleFieldExistAndAreNotEmptyReturnsFirstField()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function firstFieldDoesNotExistSecondFieldDoesExistAndIsNotEmptyReturnsSecondField()
    {
        $this->data['field2'] = 'value2';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function firstFieldDoesExistButIsEmptySecondFieldDoesExistAndIsNotEmptyReturnsSecondField()
    {
        $this->data['field1'] = '';
        $this->data['field2'] = 'value2';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function firstFieldDoesNotExistSecondFieldIsEmptyReturnsEmptyString()
    {
        $this->data['field2'] = '';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function firstFieldIsEmptySecondFieldDoesNotExistReturnsEmptyString()
    {
        $this->data['field1'] = '';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function allFieldsAreEmptyReturnsEmptyString()
    {
        $this->data['field1'] = '';
        $this->data['field2'] = '';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function noFieldExistsReturnsNull()
    {
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function fieldsAreSortedReturnsFirstField()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'firstOf' => [
                2 => ['field' => 'field2'],
                1 => ['field' => 'field1'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function fieldConditionFailsElseDoesNotExistReturnsSecondField()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'firstOf' => [
                [
                    'if' => [
                        'field1' => 'value2',
                        'then' => 'thenValue',
                    ],
                ],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function fieldConditionFailsElseDoesExistReturnsElsePart()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'firstOf' => [
                [
                    'if' => [
                        'field1' => 'value2',
                        'else' => 'elseValue',
                    ],
                ],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('elseValue', $result);
    }

    /** @test */
    public function fieldConditionSucceedsThenDoesNotExistReturnsSecondField()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'firstOf' => [
                [
                    'if' => [
                        'field1' => 'value1',
                        'else' => 'elseValue',
                    ],
                ],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function fieldConditionSucceedsThenDoesExistReturnsSecondField()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'firstOf' => [
                [
                    'if' => [
                        'field1' => 'value1',
                        'then' => 'thenValue',
                    ],
                ],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('thenValue', $result);
    }
}
