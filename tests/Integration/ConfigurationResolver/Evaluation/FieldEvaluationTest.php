<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\FieldEvaluation;

/**
 * @covers FieldEvaluation
 */
class FieldEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function fieldEqualsEvalTrue(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldEqualsEvalFalse(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => 'value4',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldDoesNotExistEqualsEvalFalse(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldEqualsNotEvalFalse(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'not' => 'value1',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldEqualsNotEvalTrue(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'not' => 'value2',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldNotEqualsEvalFalse(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'not' => [
                'field' => [
                    'field1' => 'value1',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldNotEqualsEvalTrue(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'not' => [
                'field' => [
                    'field1' => 'value2',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldKeywordEqualsEvalTrue(): void
    {
        $this->data['not'] = 'value1';
        $config = [
            'field' => [
                'not' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldKeywordEqualsEvalFalse(): void
    {
        $this->data['not'] = 'value1';
        $config = [
            'field' => [
                'not' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldKeywordDoesNotExistEqualsEvalFalse(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'not' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    public function newFieldOverwritesCurrentFieldProvider(): array
    {
        return [
            [false, false],
            [false, true],
            [true,  false],
            [true,  true],
        ];
    }

    /**
     * @dataProvider newFieldOverwritesCurrentFieldProvider
     * @test
     */
    public function newFieldOverwritesCurrentFieldEvalTrue(bool $fieldFieldImplicit, bool $secondFieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => [
                    'field' => [
                        'field1' => 'value1',
                    ],
                ],
            ],
        ];
        if ($secondFieldImplicit) {
            $config['field']['field2'] = $config['field']['field2']['field'];
        }
        if ($fieldFieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider newFieldOverwritesCurrentFieldProvider
     * @test
     */
    public function newFieldOverwritesCurrentFieldEvalFalse(bool $firstFieldImplicit, bool $secondFieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => [
                    'field' => [
                        'field1' => 'value2',
                    ],
                ],
            ],
        ];
        if ($secondFieldImplicit) {
            $config['field']['field2'] = $config['field']['field2']['field'];
        }
        if ($firstFieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
