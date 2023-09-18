<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Evaluation\FalseEvaluation
 */
class FalseEvaluationTest extends EvaluationTest
{
    protected const KEYWORD = 'false';

    /** @test */
    public function false(): void
    {
        $result = $this->processEvaluation([]);
        $this->assertFalse($result);
    }
}
