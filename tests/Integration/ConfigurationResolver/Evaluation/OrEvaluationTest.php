<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\OrEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers OrEvaluation
 */
class OrEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
    }

    /** @test */
    public function allTrue()
    {
        $config = [
            'or' => [
                'field1' => 'value1',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allFalse()
    {
        $config = [
            'or' => [
                'field1' => 'value4',
                'field2' => 'value5',
                'field3' => 'value6',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function someTrue()
    {
        $config = [
            'or' => [
                'field1' => 'value4',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function complexNestedConditionEvalTrue()
    {
        $config = [
            'or' => [
                1 => [
                    'and' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                ],
                2 => [
                    'and' => [
                        'field2' => 'value4',
                        'field3' => 'value5',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function complexNestedConditionEvalFalse()
    {
        $config = [
            'or' => [
                1 => [
                    'and' => [
                        'field1' => 'value1',
                        'field2' => 'value4',
                    ],
                ],
                2 => [
                    'and' => [
                        'field2' => 'value2',
                        'field3' => 'value5',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldLastEvalTrue()
    {
        $config = [
            'or' => [
                'equals' => 'value1',
                'field' => 'field1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldEvalTrue()
    {
        $config = [
            'or' => [
                'field' => 'field1',
                'equals' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldEvalFalse()
    {
        $config = [
            'or' => [
                'field' => 'field1',
                'equals' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFieldNonExistentFieldEvalFalse()
    {
        $config = [
            'or' => [
                'field' => 'field4',
                'equals' => 'value4',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFieldWithComplexEvaluationEvalTrue()
    {
        $config = [
            'or' => [
                'field' => 'field1',
                'and' => [
                    ['regexp' => 'value[12]'],
                    ['regexp' => 'value[0-9]'],
                ]
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldWithComplexEvaluationEvalFalse()
    {
        $config = [
            'or' => [
                'field' => 'field1',
                'and' => [
                    ['regexp' => 'value[23]'],
                    ['regexp' => 'value[0-9]'],
                ]
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldWithSubEvaluationsEvalTrue()
    {
        $config = [
            'or' => [
                'field2' => 'value1',
                'field' => 'field1',
                'equals' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldWithSubEvaluationsEvalFalse()
    {
        $config = [
            'or' => [
                'field2' => 'value1',
                'field' => 'field1',
                'equals' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexEvalTrue()
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'field' => 'field1',
                'index' => 1,
                'equals' => 'value1.2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldIndexLastEvalTrue()
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'equals' => 'value1.2',
                'field' => 'field1',
                'index' => 1,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldLastIndexEvalTrue()
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'equals' => 'value1.2',
                'index' => 1,
                'field' => 'field1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldIndexEvalFalse()
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'field' => 'field1',
                'index' => 1,
                'equals' => 'value3.3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonExistentFieldEvalFalse()
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'field' => 'field4',
                'index' => 1,
                'equals' => 'value4.2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonExistentIndexEvalFalse()
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'field' => 'field1',
                'index' => 2,
                'equals' => 'value1.3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonMultiValueEvalFalse()
    {
        $config = [
            'or' => [
                'field' => 'field1',
                'index' => 1,
                'equals' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyScalarEvalTrue()
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'or' => [
                'modify' => 'upperCase,trim',
                'field1' => 'VALUE1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyScalarEvalFalse()
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'or' => [
                'modify' => 'upperCase,trim',
                'field1' => 'VALUE2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordLastModifyScalarEvalTrue()
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'or' => [
                'field1' => 'VALUE1',
                'modify' => 'upperCase,trim',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordLastModifyScalarEvalFalse()
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'or' => [
                'field1' => 'VALUE2',
                'modify' => 'upperCase,trim',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyArrayEvalTrue()
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'or' => [
                'modify' => [
                    'upperCase' => true,
                    'trim' => true,
                ],
                'field1' => 'VALUE1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyArrayEvalFalse()
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'or' => [
                'modify' => [
                    'upperCase' => true,
                    'trim' => true,
                ],
                'field1' => 'VALUE2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordLastModifyArrayEvalTrue()
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'or' => [
                'field1' => 'VALUE1',
                'modify' => [
                    'upperCase' => true,
                    'trim' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordLastModifyArrayEvalFalse()
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'or' => [
                'field1' => 'VALUE2',
                'modify' => [
                    'upperCase' => true,
                    'trim' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
