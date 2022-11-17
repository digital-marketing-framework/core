<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\SelfEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers SelfEvaluation
 */
class SelfEvaluationTest extends AbstractEvaluationTest
{
    protected const KEY_SELF = ConfigurationResolverInterface::KEY_SELF;

    /** @test */
    public function evalFalseReturnsFalse()
    {
        $config = false;
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
    /** @test */
    public function evalSelfFalseReturnsFalse()
    {
        $config = [static::KEY_SELF => false];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalTrueReturnsTrue()
    {
        $config = true;
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfTrueReturnsTrue()
    {
        $config = [static::KEY_SELF => true];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalEmptyStringReturnsFalse()
    {
        $config = '';
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfEmptyStringReturnsFalse()
    {
        $config = [static::KEY_SELF => ''];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalNonEmptyStringReturnsTrue()
    {
        $config = 'value1';
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfNonEmptyStringReturnsTrue()
    {
        $config = [static::KEY_SELF => 'value1'];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalNullReturnsFalse()
    {
        $config = null;
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfNullReturnsFalse()
    {
        $config = [static::KEY_SELF => null];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalStringZeroReturnsFalse()
    {
        $config = '0';
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfStringZeroReturnsFalse()
    {
        $config = [static::KEY_SELF => '0'];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalStringOneReturnsTrue()
    {
        $config = '1';
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfStringOneReturnsTrue()
    {
        $config = [static::KEY_SELF => '1'];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalIntZeroReturnsFalse()
    {
        $config = 0;
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfIntZeroReturnsFalse()
    {
        $config = [static::KEY_SELF => 0];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalIntOneReturnsTrue()
    {
        $config = 1;
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfIntOneReturnsTrue()
    {
        $config = [static::KEY_SELF => 1];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalIntPositiveReturnsTrue()
    {
        $config = 42;
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfIntPositiveReturnsTrue()
    {
        $config = [static::KEY_SELF => 42];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalFieldEquals()
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => [
                static::KEY_SELF => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalFieldEqualsNot()
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => [
                static::KEY_SELF => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalFieldDoesNotExist()
    {
        $config = [
            'field1' => [
                static::KEY_SELF => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
