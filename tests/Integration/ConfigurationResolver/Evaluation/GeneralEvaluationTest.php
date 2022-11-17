<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers GeneralEvaluation
 */
class GeneralEvaluationTest extends AbstractEvaluationTest
{
    public function provider()
    {
        $scalar1 = 'value1';
        $scalar2 = 'value2';
        $array1 = ['key1' => 'value1'];
        $array2 = ['key2' => 'value2'];

        return [
            [null,     null,     false, /* => */ null],
            [null,     null,     true,  /* => */ null],
            [null,     $scalar2, false, /* => */ $scalar2],
            [null,     $scalar2, true,  /* => */ null],
            [null,     $array2,  false, /* => */ $array2],
            [null,     $array2,  true,  /* => */ null],

            ['',       null,     false, /* => */ null],
            ['',       null,     true,  /* => */ ''],
            ['',       '',       false, /* => */ ''],
            ['',       '',       true,  /* => */ ''],
            ['',       $scalar2, false, /* => */ $scalar2],
            ['',       $scalar2, true,  /* => */ ''],
            ['',       $array2,  false, /* => */ $array2],
            ['',       $array2,  true,  /* => */ ''],

            [$scalar1, null,     false, /* => */ null],
            [$scalar1, null,     true,  /* => */ $scalar1],
            [$scalar1, '',       false, /* => */ ''],
            [$scalar1, '',       true,  /* => */ $scalar1],
            [$scalar1, $scalar2, false, /* => */ $scalar2],
            [$scalar1, $scalar2, true,  /* => */ $scalar1],
            [$scalar1, $array2,  false, /* => */ $array2],
            [$scalar1, $array2,  true,  /* => */ $scalar1],

            [$array1, null,      false, /* => */ null],
            [$array1, null,      true,  /* => */ $array1],
            [$array1, '',        false, /* => */ ''],
            [$array1, '',        true,  /* => */ $array1],
            [$array1, $scalar2,  false, /* => */ $scalar2],
            [$array1, $scalar2,  true,  /* => */ $array1],
            [$array1, $array2,   false, /* => */ $array2],
            [$array1, $array2,   true,  /* => */ $array1],
        ];
    }

    protected function runThenElse($then, $else, $eval, $expected, $useNullOnThen, $useNullOnElse)
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => $eval,
        ];
        if ($then !== null || $useNullOnThen) {
            $config['then'] = $then;
        }
        if ($else !== null || $useNullOnElse) {
            $config['else'] = $else;
        }

        $result = $this->runResolverProcess($config);

        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @param $then
     * @param $else
     * @param $eval
     * @param $expected
     *
     * @dataProvider provider
     * @test
     */
    public function thenElse($then, $else, $eval, $expected)
    {
        $this->runThenElse($then, $else, $eval, $expected, false, false);
        if ($else === null) {
            $this->runThenElse($then, $else, $eval, $expected, false, true);
        }
        if ($then === null) {
            $this->runThenElse($then, $else, $eval, $expected, true, false);
        }
        if ($then === null && $else === null) {
            $this->runThenElse($then, $else, $eval, $expected, true, true);
        }
    }

    /** @test */
    public function generalEvaluationActsAsAndEvaluationTrueTrueEvalTrue()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function generalEvaluationActsAsAndEvaluationTrueFalseEvalFalse()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'field1' => 'value1',
            'field2' => 'value3',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function generalEvaluationActsAsAndEvaluationFalseTrueEvalFalse()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'field1' => 'value3',
            'field2' => 'value2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function generalEvaluationActsAsAndEvaluationFalseFalseEvalFalse()
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'field1' => 'value3',
            'field2' => 'value4',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function thenElseIsFilteredOnEvaluationEvalTrue()
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => 'value1',
            'then' => 'thenValue',
            'else' => 'elseValue',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function thenElseIsFilteredOnEvaluationEvalFalse()
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => 'value2',
            'then' => 'thenValue',
            'else' => 'elseValue',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function thenElseIsFilteredOnEvaluationNoConditionEvalTrue()
    {
        $config = [
            'then' => 'thenValue',
            'else' => 'elseValue',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }
}
