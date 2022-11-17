<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\FirstOfContentResolver;

/**
 * @covers FirstOfContentResolver
 */
class FirstOfContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function multipleFieldExistAndAreNotEmptyReturnsFirstField(): void
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
    public function firstFieldDoesNotExistSecondFieldDoesExistAndIsNotEmptyReturnsSecondField(): void
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
    public function firstFieldDoesExistButIsEmptySecondFieldDoesExistAndIsNotEmptyReturnsSecondField(): void
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
    public function firstFieldDoesNotExistSecondFieldIsEmptyReturnsEmptyString(): void
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
    public function firstFieldIsEmptySecondFieldDoesNotExistReturnsEmptyString(): void
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
    public function allFieldsAreEmptyReturnsEmptyString(): void
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
    public function noFieldExistsReturnsNull(): void
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
    public function fieldsAreSortedReturnsFirstField(): void
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
    public function fieldConditionFailsElseDoesNotExistReturnsSecondField(): void
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
    public function fieldConditionFailsElseDoesExistReturnsElsePart(): void
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
    public function fieldConditionSucceedsThenDoesNotExistReturnsSecondField(): void
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
    public function fieldConditionSucceedsThenDoesExistReturnsSecondField(): void
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
