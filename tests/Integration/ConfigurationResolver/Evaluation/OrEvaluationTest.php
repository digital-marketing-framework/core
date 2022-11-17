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
    public function allTrue(): void
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
    public function allFalse(): void
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
    public function someTrue(): void
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
    public function complexNestedConditionEvalTrue(): void
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
    public function complexNestedConditionEvalFalse(): void
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
    public function staticKeywordFieldLastEvalTrue(): void
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
    public function staticKeyWordFieldEvalTrue(): void
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
    public function staticKeyWordFieldEvalFalse(): void
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
    public function staticKeyWordFieldNonExistentFieldEvalFalse(): void
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
    public function staticKeyWordFieldWithComplexEvaluationEvalTrue(): void
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
    public function staticKeyWordFieldWithComplexEvaluationEvalFalse(): void
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
    public function staticKeywordFieldWithSubEvaluationsEvalTrue(): void
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
    public function staticKeywordFieldWithSubEvaluationsEvalFalse(): void
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
    public function staticKeywordFieldIndexEvalTrue(): void
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
    public function staticKeywordFieldIndexLastEvalTrue(): void
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
    public function staticKeywordFieldLastIndexEvalTrue(): void
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
    public function staticKeywordFieldIndexEvalFalse(): void
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
    public function staticKeywordFieldIndexNonExistentFieldEvalFalse(): void
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
    public function staticKeywordFieldIndexNonExistentIndexEvalFalse(): void
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
    public function staticKeywordFieldIndexNonMultiValueEvalFalse(): void
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
    public function staticKeyWordFirstModifyScalarEvalTrue(): void
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
    public function staticKeyWordFirstModifyScalarEvalFalse(): void
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
    public function staticKeyWordLastModifyScalarEvalTrue(): void
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
    public function staticKeyWordLastModifyScalarEvalFalse(): void
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
    public function staticKeyWordFirstModifyArrayEvalTrue(): void
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
    public function staticKeyWordFirstModifyArrayEvalFalse(): void
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
    public function staticKeyWordLastModifyArrayEvalTrue(): void
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
    public function staticKeyWordLastModifyArrayEvalFalse(): void
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
