<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\FalseEvaluation;

class FalseEvaluationTest extends EvaluationTest
{
    protected const CLASS_NAME = FalseEvaluation::class;

    protected const KEYWORD = 'false';

    /** @test */
    public function false(): void
    {
        $result = $this->processEvaluation([]);
        $this->assertFalse($result);
    }
}
