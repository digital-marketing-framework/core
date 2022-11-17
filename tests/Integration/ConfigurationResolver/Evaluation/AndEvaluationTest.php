<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers AndEvaluation
 */
class AndEvaluationTest extends AbstractEvaluationTest
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
            'and' => [
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
            'and' => [
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
            'and' => [
                'field1' => 'value4',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function complexNestedConditionEvalTrue(): void
    {
        $config = [
            'and' => [
                1 => [
                    'or' => [
                        'field1' => 'value1',
                        'field2' => 'value4',
                    ],
                ],
                2 => [
                    'or' => [
                        'field2' => 'value5',
                        'field3' => 'value3',
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
            'and' => [
                1 => [
                    'or' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                ],
                2 => [
                    'or' => [
                        'field2' => 'value4',
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
            'equals' => 'value1',
            'field' => 'field1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldEvalTrue(): void
    {
        $config = [
            'field' => 'field1',
            'equals' => 'value1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldEvalFalse(): void
    {
        $config = [
            'field' => 'field1',
            'equals' => 'value2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFieldNonExistentFieldEvalFalse(): void
    {
        $config = [
            'field' => 'field4',
            'equals' => 'value4',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFieldWithComplexEvaluationEvalTrue(): void
    {
        $config = [
            'field' => 'field1',
            'and' => [
                ['regexp' => 'value[12]'],
                ['regexp' => 'value[0-9]'],
            ]
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldWithComplexEvaluationEvalFalse(): void
    {
        $config = [
            'field' => 'field1',
            'and' => [
                ['regexp' => 'value[23]'],
                ['regexp' => 'value[0-9]'],
            ]
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldWithSubEvaluationsEvalTrue(): void
    {
        $config = [
            'field2' => 'value2',
            'field' => 'field1',
            'equals' => 'value1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldWithSubEvaluationsEvalFalse(): void
    {
        $config = [
            'field2' => 'value2',
            'field' => 'field1',
            'equals' => 'value2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field1',
            'index' => 1,
            'equals' => 'value1.2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldIndexLastEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'equals' => 'value1.2',
            'field' => 'field1',
            'index' => 1,
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldLastIndexEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'equals' => 'value1.2',
            'index' => 1,
            'field' => 'field1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldIndexEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field1',
            'index' => 1,
            'equals' => 'value3.3',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonExistentFieldEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field4',
            'index' => 1,
            'equals' => 'value4.2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonExistentIndexEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field1',
            'index' => 2,
            'equals' => 'value1.3',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonMultiValueEvalFalse(): void
    {
        $config = [
            'field' => 'field1',
            'index' => 1,
            'equals' => 'value1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyScalarEvalTrue(): void
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'modify' => 'upperCase,trim',
            'field1' => 'VALUE1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyScalarEvalFalse(): void
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'modify' => 'upperCase,trim',
            'field1' => 'VALUE2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordLastModifyScalarEvalTrue(): void
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'field1' => 'VALUE1',
            'modify' => 'upperCase,trim',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordLastModifyScalarEvalFalse(): void
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'field1' => 'VALUE2',
            'modify' => 'upperCase,trim',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyArrayEvalTrue(): void
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'modify' => [
                'upperCase' => true,
                'trim' => true,
            ],
            'field1' => 'VALUE1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyArrayEvalFalse(): void
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'modify' => [
                'upperCase' => true,
                'trim' => true,
            ],
            'field1' => 'VALUE2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordLastModifyArrayEvalTrue(): void
    {
        $this->data['field1'] = ' value1 ';
        $config = [
            'field1' => 'VALUE1',
            'modify' => [
                'upperCase' => true,
                'trim' => true,
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
            'field1' => 'VALUE2',
            'modify' => [
                'upperCase' => true,
                'trim' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
