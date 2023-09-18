<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\TrueEvaluation;

class TrueEvaluationTest extends EvaluationTest
{
    protected const CLASS_NAME = TrueEvaluation::class;

    protected const KEYWORD = 'true';

    /** @test */
    public function true(): void
    {
        $result = $this->processEvaluation([]);
        $this->assertTrue($result);
    }
}
