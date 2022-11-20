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
    public function evalFalseReturnsFalse(): void
    {
        $config = false;
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
    /** @test */
    public function evalSelfFalseReturnsFalse(): void
    {
        $config = [static::KEY_SELF => false];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalTrueReturnsTrue(): void
    {
        $config = true;
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfTrueReturnsTrue(): void
    {
        $config = [static::KEY_SELF => true];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalEmptyStringReturnsFalse(): void
    {
        $config = '';
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfEmptyStringReturnsFalse(): void
    {
        $config = [static::KEY_SELF => ''];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalNonEmptyStringReturnsTrue(): void
    {
        $config = 'value1';
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfNonEmptyStringReturnsTrue(): void
    {
        $config = [static::KEY_SELF => 'value1'];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalNullReturnsFalse(): void
    {
        $config = null;
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfNullReturnsFalse(): void
    {
        $config = [static::KEY_SELF => null];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalStringZeroReturnsFalse(): void
    {
        $config = '0';
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfStringZeroReturnsFalse(): void
    {
        $config = [static::KEY_SELF => '0'];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalStringOneReturnsTrue(): void
    {
        $config = '1';
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfStringOneReturnsTrue(): void
    {
        $config = [static::KEY_SELF => '1'];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalIntZeroReturnsFalse(): void
    {
        $config = 0;
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfIntZeroReturnsFalse(): void
    {
        $config = [static::KEY_SELF => 0];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalIntOneReturnsTrue(): void
    {
        $config = 1;
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfIntOneReturnsTrue(): void
    {
        $config = [static::KEY_SELF => 1];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalIntPositiveReturnsTrue(): void
    {
        $config = 42;
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfIntPositiveReturnsTrue(): void
    {
        $config = [static::KEY_SELF => 42];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalFieldEquals(): void
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
    public function evalFieldEqualsNot(): void
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
    public function evalFieldDoesNotExist(): void
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
