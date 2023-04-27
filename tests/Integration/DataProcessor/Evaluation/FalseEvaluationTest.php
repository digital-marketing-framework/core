<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\FalseEvaluation;

/**
 * @covers FalseEvaluation
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
