<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\IndexEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers IndexEvaluation
 */
class IndexEvaluationTest extends AbstractEvaluationTest
{
    public function implicitFieldProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexEqualsEvalTrue(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => 'value1.1',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithNonExistentIndexEqualsEvalFalse(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index2' => 'value1.1',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexEqualsEvalFalse(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => 'value2',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldDoesNotExistEqualsEvalFalse(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field2' => [
                    'index' => [
                        'index1' => 'value2.1',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexEqualsNotEvalFalse(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => [
                            'not' => 'value1.1',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithNonExistentIndexEqualsNotEvalTrue(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index2' => [
                            'not' => 'value1.1',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexEqualsNotEvalTrue(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => [
                            'not' => 'value2',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexNotEqualsEvalFalse(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'not' => [
                'field' => [
                    'field1' => [
                        'index' => [
                            'index1' => 'value1.1',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config['not'] = $config['not']['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithNonExistentIndexNotEqualsEvalTrue(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'not' => [
                'field' => [
                    'field1' => [
                        'index' => [
                            'index2' => 'value1.1',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config['not'] = $config['not']['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexNotEqualsEvalTrue(bool $fieldImplicit): void
    {
        $this->data['field1'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'not' => [
                'field' => [
                    'field1' => [
                        'index' => [
                            'index1' => 'value2',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config['not'] = $config['not']['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldKeywordWithExistingIndexEqualsEvalTrue(): void
    {
        $this->data['not'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'not' => [
                    'index' => [
                        'index1' => 'value1.1',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldKeywordWithNonExistentIndexEqualsEvalFalse(): void
    {
        $this->data['not'] = new MultiValue(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'not' => [
                    'index' => [
                        'index2' => 'value1.1',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
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
                'not' => [
                    'index' => [
                        'index1' => 'value1',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function nestedMultiValueItemEqualsScalarValueEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue([
            'index1' => new MultiValue([
                'index1_1' => 'value1_1',
                'index1_2' => 'value1_2',
            ]),
            'index2' => new MultiValue([
                'index2_1' => 'value2_1',
                'index2_2' => 'value2_2',
            ]),
        ]);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index2' => [
                            'index' => [
                                'index2_1' => 'value2_1',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function nestedMultiValueItemEqualsScalarValueEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue([
            'index1' => new MultiValue([
                'index1_1' => 'value1_1',
                'index1_2' => 'value1_2',
            ]),
            'index2' => new MultiValue([
                'index2_1' => 'value2_1',
                'index2_2' => 'value2_2',
            ]),
        ]);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index2' => [
                            'index' => [
                                'index2_1' => 'value2_2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function indexOnScalarValueEqualsScalarValueEvalFalse(bool $fieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => 'value1',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function emptyIndexClearsIndexEvalTrue(bool $fieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'someIndex' => [
                            'index' => [
                                '' => 'value1',
                            ]
                        ]
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function emptyIndexClearsIndexEvalFalse(bool $fieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'someIndex' => [
                            'index' => [
                                '' => 'value2',
                            ]
                        ]
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function emptyIndexDoesNothingIfNoIndexWasSetBeforeEvalTrue(bool $fieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        '' => 'value1',
                    ]
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function emptyIndexDoesNothingIfNoIndexWasSetBeforeEvalFalse(bool $fieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        '' => 'value2',
                    ]
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    public function newFieldClearsIndexProvider(): array
    {
        return [
            [false, false],
            [false, true],
            [true,  false],
            [true,  true],
        ];
    }

    /**
     * @dataProvider newFieldClearsIndexProvider
     * @test
     */
    public function newFieldClearsIndexEvalTrue(bool $firstFieldImplicit, bool $secondFieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => [
                    'index' => [
                        'index2' => [
                            'field' => [
                                'field1' => 'value1',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        if ($secondFieldImplicit) {
            $config['field']['field2']['index']['index2'] = $config['field']['field2']['index']['index2']['field'];
        }
        if ($firstFieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider newFieldClearsIndexProvider
     * @test
     */
    public function newFieldClearsIndexEvalFalse(bool $firstFieldImplicit, bool $secondFieldImplicit): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => [
                    'index' => [
                        'index2' => [
                            'field' => [
                                'field1' => 'value2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        if ($secondFieldImplicit) {
            $config['field']['field2']['index']['index2'] = $config['field']['field2']['index']['index2']['field'];
        }
        if ($firstFieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
