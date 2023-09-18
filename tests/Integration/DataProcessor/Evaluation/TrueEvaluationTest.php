<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Evaluation\TrueEvaluation
 */
class TrueEvaluationTest extends EvaluationTest
{
    protected const KEYWORD = 'true';

    /** @test */
    public function true(): void
    {
        $result = $this->processEvaluation([]);
        $this->assertTrue($result);
    }
}
