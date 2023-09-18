<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Evaluation\NotEvaluation
 */
class NotEvaluationTest extends EvaluationTest
{
    protected const KEYWORD = 'not';

    /** @test */
    public function notTrue(): void
    {
        $config = $this->getEvaluationConfiguration([], 'true');
        $result = $this->processEvaluation($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notFalse(): void
    {
        $config = $this->getEvaluationConfiguration([], 'false');
        $result = $this->processEvaluation($config);
        $this->assertTrue($result);
    }
}
